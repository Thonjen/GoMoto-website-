<?php

namespace App\Http\Controllers;

use App\Models\{User, Vehicle, Booking, Payment, Overcharge, BookingExtensionRequest, Role, KycVerificationLog};
use App\Services\SmsService;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    /**
     * Admin Dashboard - Overview with key metrics
     */
    public function dashboard()
    {
        // System-wide statistics
        $stats = [
            'users' => [
                'total' => User::count(),
                'new_this_month' => User::whereMonth('created_at', now()->month)->count(),
                'active_users' => User::whereHas('bookings', function($q) {
                    $q->where('created_at', '>=', now()->subDays(30));
                })->count(),
                'banned_users' => User::where('status', 'banned')->count(),
            ],
            'vehicles' => [
                'total' => Vehicle::count(),
                'available' => Vehicle::where('is_available', true)->count(),
                'pending_approval' => Vehicle::where('status', 'pending')->count(),
                'active_rentals' => Booking::where('status', 'confirmed')
                    ->whereNull('return_time')
                    ->count(),
            ],
            'bookings' => [
                'total' => Booking::count(),
                'pending' => Booking::where('status', 'pending')->count(),
                'confirmed' => Booking::where('status', 'confirmed')->count(),
                'completed' => Booking::where('status', 'completed')->count(),
                'cancelled' => Booking::where('status', 'cancelled')->count(),
            ],
            'payments' => [
                'total_revenue' => Payment::where('status', 'confirmed')->sum('amount'),
                'pending_payments' => Payment::where('status', 'pending')->sum('amount'),
                'this_month_revenue' => Payment::where('status', 'confirmed')
                    ->whereMonth('created_at', now()->month)
                    ->sum('amount'),
            ],
            'extensions' => [
                'pending' => BookingExtensionRequest::where('status', 'pending')->count(),
                'approved' => BookingExtensionRequest::where('status', 'approved')->count(),
                'rejected' => BookingExtensionRequest::where('status', 'rejected')->count(),
            ],
            'overcharges' => [
                'total_amount' => Overcharge::sum('amount'),
                'unpaid_amount' => Overcharge::where('is_paid', false)->sum('amount'),
                'total_count' => Overcharge::count(),
            ],
            'kyc' => [
                'pending_review' => User::where('kyc_status', 'under_review')->count(),
                'approved' => User::where('kyc_status', 'approved')->count(),
                'rejected' => User::where('kyc_status', 'rejected')->count(),
                'total_submissions' => User::whereNotNull('license_submitted_at')->count(),
            ]
        ];

        // Recent activities
        $recentActivities = collect([
            // Recent users
            ...User::latest()->take(5)->get()->map(function($user) {
                return [
                    'id' => 'user_' . $user->id,
                    'type' => 'user_registered',
                    'description' => "New user registered: {$user->first_name} {$user->last_name}",
                    'user' => $user->email,
                    'created_at' => $user->created_at,
                    'timestamp' => $user->created_at
                ];
            }),
            // Recent bookings
            ...Booking::with(['user', 'vehicle.make', 'vehicle.model'])->latest()->take(5)->get()->map(function($booking) {
                $vehicleName = 'Unknown Vehicle';
                if ($booking->vehicle) {
                    $makeName = $booking->vehicle->make ? $booking->vehicle->make->name : '';
                    $modelName = $booking->vehicle->model ? $booking->vehicle->model->name : '';
                    $vehicleName = trim($makeName . ' ' . $modelName) ?: 'Unknown Vehicle';
                }
                
                return [
                    'id' => 'booking_' . $booking->id,
                    'type' => 'booking_created',
                    'description' => "New booking: {$booking->user->first_name} {$booking->user->last_name}" . ($vehicleName ? " - {$vehicleName}" : ''),
                    'user' => $booking->user->email,
                    'created_at' => $booking->created_at,
                    'timestamp' => $booking->created_at
                ];
            }),
            // Recent vehicles
            ...Vehicle::with(['owner', 'make', 'model'])->latest()->take(3)->get()->map(function($vehicle) {
                $makeName = $vehicle->make ? $vehicle->make->name : '';
                $modelName = $vehicle->model ? $vehicle->model->name : '';
                $vehicleName = trim($makeName . ' ' . $modelName) ?: 'Unknown Vehicle';
                
                return [
                    'id' => 'vehicle_' . $vehicle->id,
                    'type' => 'vehicle_added',
                    'description' => "New vehicle added: {$vehicleName} by {$vehicle->owner->first_name} {$vehicle->owner->last_name}",
                    'user' => $vehicle->owner->email,
                    'created_at' => $vehicle->created_at,
                    'timestamp' => $vehicle->created_at
                ];
            })
        ])->sortByDesc('timestamp')->take(10)->values();

        // Monthly revenue chart data (last 12 months)
        $monthlyRevenue = collect(range(0, 11))->map(function($i) {
            $date = now()->subMonths($i);
            return [
                'month' => $date->format('M Y'),
                'revenue' => Payment::where('status', 'confirmed')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('amount')
            ];
        })->reverse()->values();

        // Top performing vehicles
        $topVehicles = Vehicle::withCount(['bookings' => function($q) {
                $q->where('status', 'completed');
            }])
            ->with(['make', 'model', 'owner', 'bookings' => function($q) {
                $q->where('status', 'completed')->with('payment');
            }, 'ratings'])
            ->having('bookings_count', '>', 0)
            ->orderBy('bookings_count', 'desc')
            ->take(5)
            ->get()
            ->map(function($vehicle) {
                // Calculate total revenue from completed bookings
                $totalRevenue = $vehicle->bookings->sum(function($booking) {
                    return $booking->payment ? $booking->payment->amount : 0;
                });

                // Calculate average rating from vehicle ratings
                $averageRating = $vehicle->ratings->count() > 0 ? $vehicle->ratings->avg('rating') : null;

                return [
                    'id' => $vehicle->id,
                    'make' => $vehicle->make ? $vehicle->make->name : 'Unknown',
                    'model' => $vehicle->model ? $vehicle->model->name : 'Unknown',
                    'license_plate' => $vehicle->license_plate,
                    'owner_name' => $vehicle->owner ? "{$vehicle->owner->first_name} {$vehicle->owner->last_name}" : 'Unknown Owner',
                    'total_bookings' => $vehicle->bookings_count,
                    'total_revenue' => $totalRevenue,
                    'average_rating' => $averageRating
                ];
            });

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentActivities' => $recentActivities,
            'monthlyRevenue' => $monthlyRevenue,
            'topVehicles' => $topVehicles
        ]);
    }

    /**
     * User Management
     */
    public function users(Request $request)
    {
        $query = User::with(['role', 'vehicles', 'bookings']);
        
        // Search functionality
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by role
        if ($request->role) {
            $query->whereHas('role', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $users = $query->withCount(['vehicles', 'bookings'])
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $roles = Role::all();

        return Inertia::render('Admin/Users', [
            'users' => $users,
            'roles' => $roles,
            'filters' => $request->only(['search', 'role', 'status'])
        ]);
    }

    /**
     * Vehicle Management
     */
    public function vehicles(Request $request)
    {
        $query = Vehicle::with(['owner', 'make', 'model', 'type', 'bookings']);
        
        // Search functionality
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('license_plate', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhereHas('make', function($subQ) use ($request) {
                      $subQ->where('name', 'like', '%' . $request->search . '%');
                  })
                  ->orWhereHas('model', function($subQ) use ($request) {
                      $subQ->where('name', 'like', '%' . $request->search . '%');
                  })
                  ->orWhereHas('owner', function($subQ) use ($request) {
                      $subQ->where('first_name', 'like', '%' . $request->search . '%')
                           ->orWhere('last_name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        // Filter by availability
        if ($request->has('available')) {
            $query->where('is_available', $request->boolean('available'));
        }

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $vehicles = $query->withCount(['bookings'])
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/AdminVehicles', [
            'vehicles' => $vehicles,
            'filters' => $request->only(['search', 'available', 'status'])
        ]);
    }

    /**
     * Booking Management
     */
    public function bookings(Request $request)
    {
        $query = Booking::with(['user', 'vehicle.make', 'vehicle.model', 'payment', 'overcharges']);
        
        // Search functionality
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->whereHas('user', function($subQ) use ($request) {
                    $subQ->where('first_name', 'like', '%' . $request->search . '%')
                         ->orWhere('last_name', 'like', '%' . $request->search . '%')
                         ->orWhere('email', 'like', '%' . $request->search . '%');
                })
                ->orWhereHas('vehicle', function($subQ) use ($request) {
                    $subQ->where('license_plate', 'like', '%' . $request->search . '%');
                });
            });
        }

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->date_from) {
            $query->where('pickup_datetime', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->where('pickup_datetime', '<=', $request->date_to);
        }

        $bookings = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Bookings', [
            'bookings' => $bookings,
            'filters' => $request->only(['search', 'status', 'date_from', 'date_to'])
        ]);
    }

    /**
     * Payment Management
     */
    public function payments(Request $request)
    {
        $query = Payment::with(['booking.user', 'booking.vehicle', 'paymentMode']);
        
        // Search functionality
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('reference_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('booking.user', function($subQ) use ($request) {
                      $subQ->where('first_name', 'like', '%' . $request->search . '%')
                           ->orWhere('last_name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by payment type
        if ($request->type) {
            $query->where('type', $request->type);
        }

        $payments = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Payments', [
            'payments' => $payments,
            'filters' => $request->only(['search', 'status', 'type'])
        ]);
    }

    /**
     * Extension Request Management
     */
    public function extensionRequests(Request $request)
    {
        $query = BookingExtensionRequest::with([
            'booking.user', 
            'booking.vehicle.make', 
            'booking.vehicle.model',
            'approvedBy'
        ]);
        
        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $extensions = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/ExtensionRequests', [
            'extensions' => $extensions,
            'filters' => $request->only(['status'])
        ]);
    }

    /**
     * Overcharge Management
     */
    public function overcharges(Request $request)
    {
        $query = Overcharge::with([
            'booking.user', 
            'booking.vehicle.make', 
            'booking.vehicle.model',
            'overchargeType'
        ]);
        
        // Filter by payment status
        if ($request->has('paid')) {
            $query->where('is_paid', $request->boolean('paid'));
        }

        $overcharges = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Overcharges', [
            'overcharges' => $overcharges,
            'filters' => $request->only(['paid']),
            'stats' => [
                'total_amount' => Overcharge::sum('amount'),
                'unpaid_amount' => Overcharge::where('is_paid', false)->sum('amount'),
                'paid_amount' => Overcharge::where('is_paid', true)->sum('amount'),
            ]
        ]);
    }

    /**
     * Reports and Analytics
     */
    public function reports(Request $request)
    {
        $period = $request->get('period', '30_days');
        
        // Define date range based on period
        $dateRange = match($period) {
            '7_days' => [now()->subDays(7), now()],
            '30_days' => [now()->subDays(30), now()],
            '90_days' => [now()->subDays(90), now()],
            '1_year' => [now()->subYear(), now()],
            default => [now()->subDays(30), now()]
        };

        // Revenue analytics
        $revenueData = Payment::where('status', 'confirmed')
            ->whereBetween('created_at', $dateRange)
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Booking analytics
        $bookingData = Booking::whereBetween('created_at', $dateRange)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, status')
            ->groupBy('date', 'status')
            ->orderBy('date')
            ->get()
            ->groupBy('date');

        // Top users by bookings
        $topUsers = User::withCount(['bookings' => function($q) use ($dateRange) {
                $q->whereBetween('created_at', $dateRange);
            }])
            ->orderBy('bookings_count', 'desc')
            ->take(10)
            ->get();

        // Vehicle utilization
        $vehicleUtilization = Vehicle::withCount(['bookings' => function($q) use ($dateRange) {
                $q->whereBetween('created_at', $dateRange);
            }])
            ->with(['owner', 'make', 'model'])
            ->orderBy('bookings_count', 'desc')
            ->take(10)
            ->get();

        // Vehicle category statistics
        $vehicleCategories = Vehicle::select('vehicle_types.sub_type', 'vehicle_types.category', DB::raw('COUNT(DISTINCT vehicles.id) as vehicle_count'), DB::raw('COUNT(bookings.id) as booking_count'), DB::raw('SUM(CASE WHEN bookings.status IN ("confirmed", "completed") THEN bookings.total_amount ELSE 0 END) as total_revenue'))
            ->join('vehicle_types', 'vehicles.type_id', '=', 'vehicle_types.id')
            ->leftJoin('bookings', function($join) use ($dateRange) {
                $join->on('vehicles.id', '=', 'bookings.vehicle_id')
                     ->whereBetween('bookings.created_at', $dateRange);
            })
            ->groupBy('vehicle_types.id', 'vehicle_types.sub_type', 'vehicle_types.category')
            ->orderBy('booking_count', 'desc')
            ->get()
            ->map(function($category) {
                return [
                    'name' => $category->sub_type,
                    'type' => ucfirst($category->category), // Capitalize category name
                    'vehicle_count' => $category->vehicle_count,
                    'booking_count' => $category->booking_count,
                    'total_revenue' => $category->total_revenue ?: 0,
                ];
            });

        // Platform statistics
        $platformStats = [
            'total_vehicles' => Vehicle::count(),
            'active_vehicles' => Vehicle::where('is_available', true)->count(),
            'total_users' => User::count(),
            'total_owners' => User::whereHas('role', function ($query) {
                $query->where('name', 'owner');
            })->count(),
            'total_bookings' => Booking::whereBetween('created_at', $dateRange)->count(),
            'completed_bookings' => Booking::whereBetween('created_at', $dateRange)->where('status', 'completed')->count(),
            'pending_bookings' => Booking::whereBetween('created_at', $dateRange)->where('status', 'pending')->count(),
            'total_revenue' => Payment::where('status', 'confirmed')->whereBetween('created_at', $dateRange)->sum('amount'),
        ];

        return Inertia::render('Admin/Reports', [
            'period' => $period,
            'revenueData' => $revenueData,
            'bookingData' => $bookingData,
            'topUsers' => $topUsers,
            'vehicleUtilization' => $vehicleUtilization,
            'vehicleCategories' => $vehicleCategories,
            'platformStats' => $platformStats
        ]);
    }

    /**
     * Download Reports as PDF
     */
    public function downloadReportPDF(Request $request)
    {
        $period = $request->get('period', '30_days');
        
        // Define date range based on period
        $dateRange = match($period) {
            '7_days' => [now()->subDays(7), now()],
            '30_days' => [now()->subDays(30), now()],
            '90_days' => [now()->subDays(90), now()],
            '1_year' => [now()->subYear(), now()],
            default => [now()->subDays(30), now()]
        };

        // Period label for display
        $periodLabel = match($period) {
            '7_days' => 'Last 7 Days',
            '30_days' => 'Last 30 Days',
            '90_days' => 'Last 90 Days',
            '1_year' => 'Last Year',
            default => 'Last 30 Days'
        };

        // Revenue analytics with booking count
        $revenueData = Payment::where('status', 'confirmed')
            ->whereBetween('created_at', $dateRange)
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total, COUNT(*) as booking_count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top users by bookings
        $topUsers = User::withCount(['bookings' => function($q) use ($dateRange) {
                $q->whereBetween('created_at', $dateRange);
            }])
            ->orderBy('bookings_count', 'desc')
            ->take(10)
            ->get();

        // Vehicle utilization
        $vehicleUtilization = Vehicle::withCount(['bookings' => function($q) use ($dateRange) {
                $q->whereBetween('created_at', $dateRange);
            }])
            ->with(['owner', 'make', 'model'])
            ->orderBy('bookings_count', 'desc')
            ->take(10)
            ->get();

        // Vehicle category statistics
        $vehicleCategories = Vehicle::select('vehicle_types.sub_type', 'vehicle_types.category', DB::raw('COUNT(DISTINCT vehicles.id) as vehicle_count'), DB::raw('COUNT(bookings.id) as booking_count'), DB::raw('SUM(CASE WHEN bookings.status IN ("confirmed", "completed") THEN bookings.total_amount ELSE 0 END) as total_revenue'))
            ->join('vehicle_types', 'vehicles.type_id', '=', 'vehicle_types.id')
            ->leftJoin('bookings', function($join) use ($dateRange) {
                $join->on('vehicles.id', '=', 'bookings.vehicle_id')
                     ->whereBetween('bookings.created_at', $dateRange);
            })
            ->groupBy('vehicle_types.id', 'vehicle_types.sub_type', 'vehicle_types.category')
            ->orderBy('booking_count', 'desc')
            ->get()
            ->map(function($category) {
                return [
                    'name' => $category->sub_type,
                    'type' => ucfirst($category->category),
                    'vehicle_count' => $category->vehicle_count,
                    'booking_count' => $category->booking_count,
                    'total_revenue' => $category->total_revenue ?: 0,
                ];
            });

        // Platform statistics
        $platformStats = [
            'total_vehicles' => Vehicle::count(),
            'active_vehicles' => Vehicle::where('is_available', true)->count(),
            'total_users' => User::count(),
            'total_owners' => User::whereHas('role', function ($query) {
                $query->where('name', 'owner');
            })->count(),
            'total_bookings' => Booking::whereBetween('created_at', $dateRange)->count(),
            'completed_bookings' => Booking::whereBetween('created_at', $dateRange)->where('status', 'completed')->count(),
            'pending_bookings' => Booking::whereBetween('created_at', $dateRange)->where('status', 'pending')->count(),
            'total_revenue' => Payment::where('status', 'confirmed')->whereBetween('created_at', $dateRange)->sum('amount'),
        ];

        // Calculate metrics
        $totalVehicles = $platformStats['total_vehicles'];
        $activeVehicles = $platformStats['active_vehicles'];
        $totalBookings = $platformStats['total_bookings'];
        $completedBookings = $platformStats['completed_bookings'];
        $totalRevenue = $platformStats['total_revenue'];

        $vehicleAvailability = $totalVehicles > 0 ? round(($activeVehicles / $totalVehicles) * 100) : 0;
        $completionRate = $totalBookings > 0 ? round(($completedBookings / $totalBookings) * 100) : 0;
        $averageBookingValue = $totalBookings > 0 ? $totalRevenue / $totalBookings : 0;

        // Format dates
        $startDate = Carbon::parse($dateRange[0])->format('M d, Y');
        $endDate = Carbon::parse($dateRange[1])->format('M d, Y');

        // Get admin name
        $admin = Auth::user();
        $adminName = $admin->first_name . ' ' . $admin->last_name;

        // Generate PDF
        $pdf = Pdf::loadView('pdf.reports', [
            'period' => $period,
            'periodLabel' => $periodLabel,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'adminName' => $adminName,
            'revenueData' => $revenueData,
            'topUsers' => $topUsers,
            'vehicleUtilization' => $vehicleUtilization,
            'vehicleCategories' => $vehicleCategories,
            'platformStats' => $platformStats,
            'vehicleAvailability' => $vehicleAvailability,
            'completionRate' => $completionRate,
            'averageBookingValue' => $averageBookingValue,
        ])->setPaper('a4', 'portrait');

        // Generate filename
        $filename = 'GoMoto_Analytics_Report_' . $periodLabel . '_' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * User Actions
     */
    public function banUser(User $user)
    {
        // Prevent banning yourself
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot ban yourself.');
        }
        
        // Prevent banning other administrators
        if ($user->role && $user->role->name === 'admin') {
            return back()->with('error', 'You cannot ban other administrators.');
        }
        
        $user->update(['status' => 'banned']);
        
        // Cancel all active bookings
        $user->bookings()->where('status', 'confirmed')->update(['status' => 'cancelled']);
        
        return back()->with('success', 'User has been banned successfully.');
    }

    public function unbanUser(User $user)
    {
        $user->update(['status' => 'active']);
        
        return back()->with('success', 'User has been unbanned successfully.');
    }

    public function updateUserRole(Request $request, User $user)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id'
        ]);

        // Prevent changing your own role
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot change your own role.');
        }
        
        // Prevent changing other administrators' roles
        if ($user->role && $user->role->name === 'admin') {
            return back()->with('error', 'You cannot change other administrators\' roles.');
        }

        $oldRoleId = $user->role_id;
        $user->update(['role_id' => $request->role_id]);
        
        // If user is being promoted to admin, grant them full permissions
        $newRole = \App\Models\Role::find($request->role_id);
        if ($newRole && $newRole->name === 'admin') {
            $user->update([
                'can_book' => true,
                'can_list_vehicles' => true,
                'kyc_status' => 'approved',
                'kyc_verified_at' => now(),
                'email_verified_at' => now(),
                'status' => 'active'
            ]);
        }
        
        return back()->with('success', 'User role updated successfully.');
    }

    /**
     * Vehicle Actions
     */
    public function approveVehicle(Vehicle $vehicle)
    {
        $vehicle->update(['status' => 'approved', 'is_available' => true]);
        
        return back()->with('success', 'Vehicle has been approved successfully.');
    }

    public function rejectVehicle(Vehicle $vehicle)
    {
        $vehicle->update(['status' => 'rejected', 'is_available' => false]);
        
        return back()->with('success', 'Vehicle has been rejected.');
    }

    public function suspendVehicle(Vehicle $vehicle)
    {
        $vehicle->update(['status' => 'suspended', 'is_available' => false]);
        
        // Cancel all active bookings for this vehicle
        $vehicle->bookings()->where('status', 'confirmed')->update(['status' => 'cancelled']);
        
        return back()->with('success', 'Vehicle has been suspended.');
    }

    /**
     * Booking Actions
     */
    public function cancelBooking(Booking $booking)
    {
        $booking->update(['status' => 'cancelled']);
        
        return back()->with('success', 'Booking has been cancelled.');
    }

    public function refundBooking(Request $request, Booking $booking)
    {
        $request->validate([
            'refund_amount' => 'required|numeric|min:0|max:' . $booking->total_amount,
            'refund_reason' => 'required|string|max:500'
        ]);

        // Create refund payment record
        Payment::create([
            'booking_id' => $booking->id,
            'amount' => -$request->refund_amount, // Negative amount for refund
            'type' => 'refund',
            'reference_number' => 'REFUND-' . time(),
            'status' => 'completed',
            'paid_at' => now(),
            'notes' => $request->refund_reason
        ]);

        $booking->update(['status' => 'refunded']);
        
        return back()->with('success', 'Refund has been processed successfully.');
    }

    /**
     * System Settings
     */
    public function settings()
    {
        // System configuration settings
        $settings = [
            'maintenance_mode' => config('app.maintenance', false),
            'registration_enabled' => true, // This could be stored in database
            'max_booking_advance_days' => 30,
            'default_grace_period' => 15,
            'platform_commission' => 10, // percentage
        ];

        return Inertia::render('Admin/Settings', [
            'settings' => $settings
        ]);
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'maintenance_mode' => 'boolean',
            'registration_enabled' => 'boolean',
            'max_booking_advance_days' => 'integer|min:1|max:365',
            'default_grace_period' => 'integer|min:0|max:60',
            'platform_commission' => 'numeric|min:0|max:50',
        ]);

        // Here you would typically store these in a settings table or config
        // For now, we'll just return success
        
        return back()->with('success', 'Settings updated successfully.');
    }

    /**
     * KYC Verification Management
     */
    public function kycVerifications(Request $request)
    {
        $query = User::with(['role', 'kycVerificationLogs.admin'])
            ->where(function($q) {
                $q->where('kyc_status', '!=', 'pending')
                  ->orWhereNotNull('drivers_license_front')
                  ->orWhereNotNull('drivers_license_back');
            });

        // Filter by status
        if ($request->filled('status')) {
            $query->where('kyc_status', $request->status);
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->whereHas('role', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('license_submitted_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'pending_review' => User::where('kyc_status', 'under_review')->count(),
            'approved' => User::where('kyc_status', 'approved')->count(),
            'rejected' => User::where('kyc_status', 'rejected')->count(),
            'total_submissions' => User::whereNotNull('license_submitted_at')->count(),
        ];

        $roles = Role::whereIn('name', ['renter', 'owner'])->get();

        return Inertia::render('Admin/KycVerifications', [
            'users' => $users,
            'stats' => $stats,
            'roles' => $roles,
            'filters' => $request->only(['search', 'status', 'role'])
        ]);
    }

    public function approveKyc(Request $request, User $user)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500'
        ]);

        $oldStatus = $user->kyc_status;

        $user->update([
            'kyc_status' => 'approved',
            'kyc_verified_at' => now(),
            'kyc_verified_by' => Auth::id(),
            'kyc_rejection_reason' => null,
            'can_book' => true,
            'can_list_vehicles' => $user->role->name === 'owner' ? true : $user->can_list_vehicles,
        ]);

        // Log the approval
        KycVerificationLog::create([
            'user_id' => $user->id,
            'admin_id' => Auth::id(),
            'action' => 'approved',
            'old_status' => $oldStatus,
            'new_status' => 'approved',
            'reason' => $request->reason,
            'documents_checked' => [
                'drivers_license_front' => true,
                'drivers_license_back' => true,
                'bio_reviewed' => !empty($user->bio)
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        // Send SMS notification to user about successful verification (best-effort)
        try {
            $sms = new SmsService();
            $sent = $sms->sendVerificationToUser($user);
            if ($sent) {
                Log::info('AdminController: KYC approval SMS sent', ['admin_id' => Auth::id(), 'user_id' => $user->id, 'phone' => $user->phone]);
            } else {
                Log::warning('AdminController: KYC approval SMS failed to send', ['admin_id' => Auth::id(), 'user_id' => $user->id, 'phone' => $user->phone]);
            }
        } catch (\Throwable $e) {
            Log::error('AdminController: exception when sending KYC approval SMS', ['error' => $e->getMessage(), 'admin_id' => Auth::id(), 'user_id' => $user->id]);
            // don't interrupt the admin flow if SMS fails; it is logged inside service
        }

        return back()->with('success', "KYC approved for {$user->first_name} {$user->last_name}");
    }

    public function rejectKyc(Request $request, User $user)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $oldStatus = $user->kyc_status;

        $user->update([
            'kyc_status' => 'rejected',
            'kyc_rejection_reason' => $request->reason,
            'can_book' => false,
            'can_list_vehicles' => false,
        ]);

        // Log the rejection
        KycVerificationLog::create([
            'user_id' => $user->id,
            'admin_id' => Auth::id(),
            'action' => 'rejected',
            'old_status' => $oldStatus,
            'new_status' => 'rejected',
            'reason' => $request->reason,
            'documents_checked' => [
                'drivers_license_front' => !empty($user->drivers_license_front),
                'drivers_license_back' => !empty($user->drivers_license_back),
                'bio_reviewed' => !empty($user->bio)
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return back()->with('success', "KYC rejected for {$user->first_name} {$user->last_name}");
    }

    /**
     * Send a one-off KYC status notification to the user via SMS or Email.
     * Uses the SmsService and Mail facade, choosing the message content based on current kyc_status.
     */
    public function notifyKycStatus(Request $request, User $user)
    {
        $request->validate([
            'notification_type' => 'required|in:sms,email'
        ]);

        $notificationType = $request->notification_type;

        Log::info('AdminController: notifyKycStatus called', [
            'admin_id' => Auth::id(), 
            'user_id' => $user->id, 
            'kyc_status' => $user->kyc_status,
            'notification_type' => $notificationType
        ]);

        if ($user->kyc_status === 'approved') {
            if ($notificationType === 'sms') {
                $sms = new SmsService();
                $sent = $sms->sendVerificationToUser($user);
                if ($sent) {
                    Log::info('AdminController: manual KYC approved SMS sent', ['admin_id' => Auth::id(), 'user_id' => $user->id, 'phone' => $user->phone]);
                    return back()->with('success', "SMS notification sent to {$user->first_name} {$user->last_name}");
                }
                Log::warning('AdminController: manual KYC approved SMS failed', ['admin_id' => Auth::id(), 'user_id' => $user->id, 'phone' => $user->phone]);
                return back()->with('error', 'Failed to send SMS notification.');
            } else {
                // Send email notification
                try {
                    \Illuminate\Support\Facades\Mail::send('emails.kyc-verified', ['user' => $user], function ($message) use ($user) {
                        $message->to($user->email)
                                ->subject('Identity Verified - GoMoto');
                    });
                    Log::info('AdminController: manual KYC approved email sent', ['admin_id' => Auth::id(), 'user_id' => $user->id, 'email' => $user->email]);
                    return back()->with('success', "Email notification sent to {$user->first_name} {$user->last_name}");
                } catch (\Throwable $e) {
                    Log::error('AdminController: exception when sending KYC approval email', ['error' => $e->getMessage(), 'admin_id' => Auth::id(), 'user_id' => $user->id]);
                    return back()->with('error', 'Failed to send email notification.');
                }
            }
        }

        if ($user->kyc_status === 'rejected') {
            if ($notificationType === 'sms') {
                $sms = new SmsService();
                $reason = $user->kyc_rejection_reason ? ' Reason: ' . $user->kyc_rejection_reason : '';
                $text = "Hi {$user->first_name}, your driver's license verification was rejected." . $reason . " Please review and resubmit.";
                $sent = $sms->send($user->phone, $text);
                if ($sent) {
                    Log::info('AdminController: manual KYC rejected SMS sent', ['admin_id' => Auth::id(), 'user_id' => $user->id, 'phone' => $user->phone]);
                    return back()->with('success', "SMS notification sent to {$user->first_name} {$user->last_name}");
                }
                Log::warning('AdminController: manual KYC rejected SMS failed', ['admin_id' => Auth::id(), 'user_id' => $user->id, 'phone' => $user->phone]);
                return back()->with('error', 'Failed to send SMS notification.');
            } else {
                // Send rejection email notification
                try {
                    \Illuminate\Support\Facades\Mail::send('emails.kyc-rejected', [
                        'user' => $user,
                        'reason' => $user->kyc_rejection_reason
                    ], function ($message) use ($user) {
                        $message->to($user->email)
                                ->subject('KYC Verification Update - GoMoto');
                    });
                    Log::info('AdminController: manual KYC rejected email sent', ['admin_id' => Auth::id(), 'user_id' => $user->id, 'email' => $user->email]);
                    return back()->with('success', "Email notification sent to {$user->first_name} {$user->last_name}");
                } catch (\Throwable $e) {
                    Log::error('AdminController: exception when sending KYC rejection email', ['error' => $e->getMessage(), 'admin_id' => Auth::id(), 'user_id' => $user->id]);
                    return back()->with('error', 'Failed to send email notification.');
                }
            }
        }

        Log::warning('AdminController: notifyKycStatus called but user has unsupported kyc_status', ['admin_id' => Auth::id(), 'user_id' => $user->id, 'kyc_status' => $user->kyc_status]);
        return back()->with('error', 'User KYC status is neither approved nor rejected.');
    }
    public function kycLogs(Request $request)
    {
        $query = KycVerificationLog::with(['user', 'admin']);

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by admin
        if ($request->filled('admin_id')) {
            $query->where('admin_id', $request->admin_id);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();
            
        // Append action_label accessor to each log
        $logs->getCollection()->transform(function ($log) {
            $log->append('action_label');
            return $log;
        });

        $stats = [
            'total_actions' => KycVerificationLog::count(),
            'approvals' => KycVerificationLog::where('action', 'approved')->count(),
            'rejections' => KycVerificationLog::where('action', 'rejected')->count(),
            'submissions' => KycVerificationLog::whereIn('action', ['submitted', 'resubmitted'])->count(),
        ];

        $admins = User::whereHas('role', function($q) {
            $q->where('name', 'admin');
        })->get(['id', 'first_name', 'last_name']);

        return Inertia::render('Admin/KycLogs', [
            'logs' => $logs,
            'stats' => $stats,
            'admins' => $admins,
            'filters' => $request->only(['user_id', 'admin_id', 'action', 'date_from', 'date_to'])
        ]);
    }
}
