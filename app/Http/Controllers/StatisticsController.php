<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class StatisticsController extends Controller
{
    /**
     * Admin statistics dashboard
     */
    public function adminIndex()
    {
        // Check if user is admin
        if (!Auth::user()->role || Auth::user()->role->name !== 'admin') {
            abort(403, 'Unauthorized');
        }

        // Get most rented vehicles (by booking count)
        $mostRentedVehicles = Vehicle::select('vehicles.id', 'vehicles.license_plate', 'vehicles.owner_id', 'vehicles.make_id', 'vehicles.model_id', 'vehicles.type_id', DB::raw('COUNT(bookings.id) as booking_count'))
            ->leftJoin('bookings', 'vehicles.id', '=', 'bookings.vehicle_id')
            ->with(['make', 'model', 'type', 'owner'])
            ->groupBy('vehicles.id', 'vehicles.license_plate', 'vehicles.owner_id', 'vehicles.make_id', 'vehicles.model_id', 'vehicles.type_id')
            ->orderBy('booking_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'name' => $vehicle->make->name . ' ' . $vehicle->model->name,
                    'type' => $vehicle->type->sub_type,
                    'owner' => $vehicle->owner->name,
                    'booking_count' => $vehicle->booking_count,
                    'plate_number' => $vehicle->license_plate,
                ];
            });

        // Get highest earning vehicles
        $highestEarningVehicles = Vehicle::select('vehicles.id', 'vehicles.license_plate', 'vehicles.owner_id', 'vehicles.make_id', 'vehicles.model_id', 'vehicles.type_id', DB::raw('SUM(bookings.total_amount) as total_earnings'))
            ->leftJoin('bookings', 'vehicles.id', '=', 'bookings.vehicle_id')
            ->whereIn('bookings.status', ['confirmed', 'completed'])
            ->with(['make', 'model', 'type', 'owner'])
            ->groupBy('vehicles.id', 'vehicles.license_plate', 'vehicles.owner_id', 'vehicles.make_id', 'vehicles.model_id', 'vehicles.type_id')
            ->orderBy('total_earnings', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'name' => $vehicle->make->name . ' ' . $vehicle->model->name,
                    'type' => $vehicle->type->sub_type,
                    'owner' => $vehicle->owner->name,
                    'total_earnings' => $vehicle->total_earnings ?: 0,
                    'plate_number' => $vehicle->license_plate,
                ];
            });

        // Get vehicle type distribution
        $vehicleTypeStats = Vehicle::select('vehicle_types.sub_type', DB::raw('COUNT(*) as count'))
            ->join('vehicle_types', 'vehicles.type_id', '=', 'vehicle_types.id')
            ->groupBy('vehicle_types.sub_type')
            ->orderBy('count', 'desc')
            ->get();

        // Get monthly booking trends (last 6 months)
        $monthlyBookings = Booking::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => Carbon::create($item->year, $item->month)->format('M Y'),
                    'bookings' => $item->count,
                    'revenue' => $item->revenue ?: 0,
                ];
            });

        // Get top performing owners
        $topOwners = User::select('users.id', 'users.first_name', 'users.last_name', 'users.email', DB::raw('COUNT(bookings.id) as total_bookings'), DB::raw('SUM(bookings.total_amount) as total_revenue'))
            ->join('vehicles', 'users.id', '=', 'vehicles.owner_id')
            ->leftJoin('bookings', 'vehicles.id', '=', 'bookings.vehicle_id')
            ->whereIn('bookings.status', ['confirmed', 'completed'])
            ->groupBy('users.id', 'users.first_name', 'users.last_name', 'users.email')
            ->orderBy('total_revenue', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($owner) {
                return [
                    'id' => $owner->id,
                    'name' => trim($owner->first_name . ' ' . $owner->last_name),
                    'email' => $owner->email,
                    'total_bookings' => $owner->total_bookings,
                    'total_revenue' => $owner->total_revenue ?: 0,
                    'vehicle_count' => $owner->vehicles()->count(),
                ];
            });

        // General platform statistics
        $platformStats = [
            'total_vehicles' => Vehicle::count(),
            'active_vehicles' => Vehicle::where('is_available', true)->count(),
            'total_users' => User::count(),
            'total_owners' => User::whereHas('role', function ($query) {
                $query->where('name', 'owner');
            })->count(),
            'total_bookings' => Booking::count(),
            'completed_bookings' => Booking::where('status', 'completed')->count(),
            'total_revenue' => Booking::whereIn('status', ['confirmed', 'completed'])->sum('total_amount'),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
        ];

        // Average booking duration
        $avgBookingDuration = Booking::whereNotNull('pickup_datetime')
            ->whereNotNull('return_time')
            ->get()
            ->avg(function ($booking) {
                return Carbon::parse($booking->pickup_datetime)->diffInHours(Carbon::parse($booking->return_time));
            });

        return Inertia::render('Admin/Statistics', [
            'mostRentedVehicles' => $mostRentedVehicles,
            'highestEarningVehicles' => $highestEarningVehicles,
            'vehicleTypeStats' => $vehicleTypeStats,
            'monthlyBookings' => $monthlyBookings,
            'topOwners' => $topOwners,
            'platformStats' => $platformStats,
            'avgBookingDuration' => round($avgBookingDuration ?: 0, 1),
        ]);
    }

    /**
     * Owner statistics dashboard
     */
    public function ownerIndex(Request $request)
    {
        // Check if user is owner
        if (!Auth::user()->role || Auth::user()->role->name !== 'owner') {
            abort(403, 'Unauthorized');
        }

        $userId = Auth::id();
        $period = $request->get('period', '30_days');
        $dateRange = $this->getDateRangeFromPeriod($period);

        // Get owner's vehicle performance
        $vehiclePerformance = Vehicle::where('owner_id', $userId)
            ->select('vehicles.id', 'vehicles.license_plate', 'vehicles.is_available', 'vehicles.make_id', 'vehicles.model_id', 'vehicles.type_id',
                DB::raw('COUNT(bookings.id) as booking_count'),
                DB::raw('SUM(CASE WHEN bookings.status IN ("confirmed", "completed") THEN bookings.total_amount ELSE 0 END) as total_earnings'),
                DB::raw('AVG(vehicle_ratings.rating) as avg_rating'),
                DB::raw('COUNT(DISTINCT vehicle_ratings.id) as rating_count')
            )
            ->leftJoin('bookings', 'vehicles.id', '=', 'bookings.vehicle_id')
            ->leftJoin('vehicle_ratings', 'vehicles.id', '=', 'vehicle_ratings.vehicle_id')
            ->with(['make', 'model', 'type'])
            ->groupBy('vehicles.id', 'vehicles.license_plate', 'vehicles.is_available', 'vehicles.make_id', 'vehicles.model_id', 'vehicles.type_id')
            ->orderBy('total_earnings', 'desc')
            ->get()
            ->map(function ($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'name' => $vehicle->make->name . ' ' . $vehicle->model->name,
                    'type' => $vehicle->type->sub_type,
                    'plate_number' => $vehicle->license_plate,
                    'booking_count' => $vehicle->booking_count,
                    'total_earnings' => $vehicle->total_earnings ?: 0,
                    'avg_rating' => $vehicle->avg_rating ? round($vehicle->avg_rating, 1) : null,
                    'rating_count' => $vehicle->rating_count,
                    'is_available' => $vehicle->is_available,
                ];
            });

        // Monthly earnings trend (last 6 months)
        $monthlyEarnings = Booking::whereHas('vehicle', function ($query) use ($userId) {
                $query->where('owner_id', $userId);
            })
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as bookings'),
                DB::raw('SUM(total_amount) as earnings')
            )
            ->where('created_at', '>=', $dateRange['start'])
            ->where('created_at', '<=', $dateRange['end'])
            ->whereIn('status', ['confirmed', 'completed'])
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => Carbon::create($item->year, $item->month)->format('M Y'),
                    'bookings' => $item->bookings,
                    'earnings' => $item->earnings ?: 0,
                ];
            });

        // Booking status distribution
        $bookingStatusStats = Booking::whereHas('vehicle', function ($query) use ($userId) {
                $query->where('owner_id', $userId);
            })
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->status => $item->count];
            });

        // Peak booking hours
        $peakHours = Booking::whereHas('vehicle', function ($query) use ($userId) {
                $query->where('owner_id', $userId);
            })
            ->whereNotNull('pickup_datetime')
            ->select(DB::raw('HOUR(pickup_datetime) as hour'), DB::raw('COUNT(*) as count'))
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $hour = $item->hour;
                $timeFormat = $hour == 0 ? '12:00 AM' : 
                             ($hour < 12 ? $hour . ':00 AM' : 
                             ($hour == 12 ? '12:00 PM' : ($hour - 12) . ':00 PM'));
                return [
                    'hour' => $timeFormat,
                    'count' => $item->count,
                ];
            });

        // Owner statistics summary
        $ownerStats = [
            'total_vehicles' => Vehicle::where('owner_id', $userId)->count(),
            'active_vehicles' => Vehicle::where('owner_id', $userId)->where('is_available', true)->count(),
            'total_bookings' => Booking::whereHas('vehicle', function ($query) use ($userId) {
                $query->where('owner_id', $userId);
            })->count(),
            'completed_bookings' => Booking::whereHas('vehicle', function ($query) use ($userId) {
                $query->where('owner_id', $userId);
            })->where('status', 'completed')->count(),
            'total_earnings' => Booking::whereHas('vehicle', function ($query) use ($userId) {
                $query->where('owner_id', $userId);
            })->whereIn('status', ['confirmed', 'completed'])->sum('total_amount'),
            'pending_bookings' => Booking::whereHas('vehicle', function ($query) use ($userId) {
                $query->where('owner_id', $userId);
            })->where('status', 'pending')->count(),
            'avg_vehicle_rating' => $vehiclePerformance->avg('avg_rating') ? round($vehiclePerformance->avg('avg_rating'), 1) : null,
        ];

        // Recent customer feedback
        $recentFeedback = DB::table('vehicle_ratings')
            ->join('vehicles', 'vehicle_ratings.vehicle_id', '=', 'vehicles.id')
            ->join('users', 'vehicle_ratings.user_id', '=', 'users.id')
            ->where('vehicles.owner_id', $userId)
            ->whereNotNull('vehicle_ratings.comment')
            ->select(
                'vehicle_ratings.*', 
                DB::raw("CONCAT(COALESCE(users.first_name, ''), ' ', COALESCE(users.last_name, '')) as customer_name"), 
                'vehicles.license_plate as plate_number'
            )
            ->orderBy('vehicle_ratings.created_at', 'desc')
            ->limit(5)
            ->get();

        return Inertia::render('Owner/Statistics', [
            'vehiclePerformance' => $vehiclePerformance,
            'monthlyEarnings' => $monthlyEarnings,
            'bookingStatusStats' => $bookingStatusStats,
            'peakHours' => $peakHours,
            'ownerStats' => $ownerStats,
            'recentFeedback' => $recentFeedback,
            'period' => $period,
        ]);
    }

    /**
     * Generate PDF report for owner statistics
     */
    public function ownerPDF(Request $request)
    {
        // Check if user is owner
        if (!Auth::user()->role || Auth::user()->role->name !== 'owner') {
            abort(403, 'Unauthorized');
        }

        $userId = Auth::id();
        $period = $request->get('period', '30_days');
        $dateRange = $this->getDateRangeFromPeriod($period);

        // Get owner's vehicle performance
        $vehiclePerformance = Vehicle::where('owner_id', $userId)
            ->select('vehicles.id', 'vehicles.license_plate', 'vehicles.is_available', 'vehicles.make_id', 'vehicles.model_id', 'vehicles.type_id',
                DB::raw('COUNT(bookings.id) as booking_count'),
                DB::raw('SUM(CASE WHEN bookings.status IN ("confirmed", "completed") THEN bookings.total_amount ELSE 0 END) as total_earnings'),
                DB::raw('AVG(vehicle_ratings.rating) as avg_rating')
            )
            ->leftJoin('bookings', 'vehicles.id', '=', 'bookings.vehicle_id')
            ->leftJoin('vehicle_ratings', 'vehicles.id', '=', 'vehicle_ratings.vehicle_id')
            ->with(['make', 'model', 'type'])
            ->groupBy('vehicles.id', 'vehicles.license_plate', 'vehicles.is_available', 'vehicles.make_id', 'vehicles.model_id', 'vehicles.type_id')
            ->orderBy('total_earnings', 'desc')
            ->get()
            ->map(function ($vehicle) {
                return (object)[
                    'id' => $vehicle->id,
                    'name' => $vehicle->make->name . ' ' . $vehicle->model->name,
                    'plate_number' => $vehicle->license_plate,
                    'booking_count' => $vehicle->booking_count,
                    'total_earnings' => $vehicle->total_earnings ?: 0,
                    'avg_rating' => $vehicle->avg_rating ? round($vehicle->avg_rating, 1) : null,
                    'is_available' => $vehicle->is_available,
                ];
            });

        // Monthly earnings trend
        $monthlyEarnings = Booking::whereHas('vehicle', function ($query) use ($userId) {
                $query->where('owner_id', $userId);
            })
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as bookings'),
                DB::raw('SUM(total_amount) as earnings')
            )
            ->where('created_at', '>=', $dateRange['start'])
            ->where('created_at', '<=', $dateRange['end'])
            ->whereIn('status', ['confirmed', 'completed'])
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => Carbon::create($item->year, $item->month)->format('M Y'),
                    'bookings' => $item->bookings,
                    'earnings' => $item->earnings ?: 0,
                ];
            });

        // Booking status distribution
        $bookingStatusStats = Booking::whereHas('vehicle', function ($query) use ($userId) {
                $query->where('owner_id', $userId);
            })
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->status => $item->count];
            });

        // Peak booking hours
        $peakHours = Booking::whereHas('vehicle', function ($query) use ($userId) {
                $query->where('owner_id', $userId);
            })
            ->whereNotNull('pickup_datetime')
            ->select(DB::raw('HOUR(pickup_datetime) as hour'), DB::raw('COUNT(*) as count'))
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->limit(8)
            ->get()
            ->map(function ($item) {
                $hour = $item->hour;
                $timeFormat = $hour == 0 ? '12:00 AM' : 
                             ($hour < 12 ? $hour . ':00 AM' : 
                             ($hour == 12 ? '12:00 PM' : ($hour - 12) . ':00 PM'));
                return (object)[
                    'hour' => $timeFormat,
                    'count' => $item->count,
                ];
            });

        // Owner statistics summary
        $ownerStats = [
            'total_vehicles' => Vehicle::where('owner_id', $userId)->count(),
            'active_vehicles' => Vehicle::where('owner_id', $userId)->where('is_available', true)->count(),
            'total_bookings' => Booking::whereHas('vehicle', function ($query) use ($userId) {
                $query->where('owner_id', $userId);
            })->count(),
            'completed_bookings' => Booking::whereHas('vehicle', function ($query) use ($userId) {
                $query->where('owner_id', $userId);
            })->where('status', 'completed')->count(),
            'total_earnings' => Booking::whereHas('vehicle', function ($query) use ($userId) {
                $query->where('owner_id', $userId);
            })->whereIn('status', ['confirmed', 'completed'])->sum('total_amount'),
            'pending_bookings' => Booking::whereHas('vehicle', function ($query) use ($userId) {
                $query->where('owner_id', $userId);
            })->where('status', 'pending')->count(),
            'avg_vehicle_rating' => $vehiclePerformance->avg('avg_rating') ? round($vehiclePerformance->avg('avg_rating'), 1) : null,
        ];

        // Recent customer feedback
        $recentFeedback = DB::table('vehicle_ratings')
            ->join('vehicles', 'vehicle_ratings.vehicle_id', '=', 'vehicles.id')
            ->join('users', 'vehicle_ratings.user_id', '=', 'users.id')
            ->where('vehicles.owner_id', $userId)
            ->whereNotNull('vehicle_ratings.comment')
            ->select(
                'vehicle_ratings.*', 
                DB::raw("CONCAT(COALESCE(users.first_name, ''), ' ', COALESCE(users.last_name, '')) as customer_name"), 
                'vehicles.license_plate as plate_number'
            )
            ->orderBy('vehicle_ratings.created_at', 'desc')
            ->limit(5)
            ->get();

        // Calculate metrics
        $completionRate = $ownerStats['total_bookings'] > 0 
            ? round(($ownerStats['completed_bookings'] / $ownerStats['total_bookings']) * 100) 
            : 0;
        
        $vehicleAvailability = $ownerStats['total_vehicles'] > 0 
            ? round(($ownerStats['active_vehicles'] / $ownerStats['total_vehicles']) * 100) 
            : 0;
        
        $averageBookingValue = $ownerStats['total_bookings'] > 0 
            ? round($ownerStats['total_earnings'] / $ownerStats['total_bookings']) 
            : 0;

        $periodLabel = $this->getPeriodLabel($period);
        $ownerName = Auth::user()->first_name . ' ' . Auth::user()->last_name;

        $pdf = \PDF::loadView('pdf.statistics', [
            'ownerName' => $ownerName,
            'periodLabel' => $periodLabel,
            'startDate' => $dateRange['start']->format('M d, Y'),
            'endDate' => $dateRange['end']->format('M d, Y'),
            'vehiclePerformance' => $vehiclePerformance,
            'monthlyEarnings' => $monthlyEarnings,
            'bookingStatusStats' => $bookingStatusStats,
            'peakHours' => $peakHours,
            'ownerStats' => $ownerStats,
            'recentFeedback' => $recentFeedback,
            'completionRate' => $completionRate,
            'vehicleAvailability' => $vehicleAvailability,
            'averageBookingValue' => $averageBookingValue,
        ]);

        return $pdf->download($ownerName . '_Business_Analytics_' . $periodLabel . '.pdf');
    }

    /**
     * Get date range based on period
     */
    private function getDateRangeFromPeriod($period)
    {
        $end = Carbon::now()->endOfDay();
        $start = match($period) {
            '7_days' => Carbon::now()->subDays(7)->startOfDay(),
            '30_days' => Carbon::now()->subDays(30)->startOfDay(),
            '90_days' => Carbon::now()->subDays(90)->startOfDay(),
            '1_year' => Carbon::now()->subYear()->startOfDay(),
            default => Carbon::now()->subDays(30)->startOfDay(),
        };

        return ['start' => $start, 'end' => $end];
    }

    /**
     * Get period label for display
     */
    private function getPeriodLabel($period)
    {
        return match($period) {
            '7_days' => 'Last 7 Days',
            '30_days' => 'Last 30 Days',
            '90_days' => 'Last 90 Days',
            '1_year' => 'Last Year',
            default => 'Last 30 Days',
        };
    }
}
