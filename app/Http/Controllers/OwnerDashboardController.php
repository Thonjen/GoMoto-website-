<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class OwnerDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get owner's vehicles
        $vehicles = Vehicle::where('owner_id', $user->id)
            ->with(['make', 'type', 'pricingTiers'])
            ->get();

        $vehicleIds = $vehicles->pluck('id');

        // Vehicle Statistics
        $vehicleStats = [
            'total_vehicles' => $vehicles->count(),
            'available_vehicles' => $vehicles->where('is_available', true)->count(),
            'unavailable_vehicles' => $vehicles->where('is_available', false)->count(),
            'currently_booked' => $this->getCurrentlyBookedCount($vehicleIds),
        ];

        // Recent Bookings (last 10)
        $recentBookings = Booking::whereIn('vehicle_id', $vehicleIds)
            ->with([
                'user',
                'vehicle.make',
                'vehicle.type',
                'payment.paymentMode',
                'overcharges'
            ])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($booking) {
                $expectedReturn = $booking->getCalculatedEndDatetimeAttribute();
                
                return [
                    'id' => $booking->id,
                    'vehicle_name' => ($booking->vehicle->make->name ?? 'Unknown') . ' ' . ($booking->vehicle->type->sub_type ?? 'Vehicle'),
                    'vehicle_image' => $booking->vehicle->main_photo_url ?? '/images/placeholder-vehicle.jpg',
                    'renter_name' => $booking->user->first_name . ' ' . $booking->user->last_name,
                    'renter_phone' => $booking->user->phone,
                    'pickup_datetime' => $booking->pickup_datetime,
                    'expected_return' => $expectedReturn,
                    'actual_return_time' => $booking->actual_return_time,
                    'status' => $booking->status,
                    'total_amount' => $booking->total_amount,
                    'payment_status' => $booking->payment?->paid_at ? 'paid' : 'pending',
                    'has_overcharges' => $booking->has_overcharges,
                    'total_overcharges' => $booking->total_overcharges ?? 0,
                    'is_overdue' => $booking->status === 'confirmed' && !$booking->actual_return_time && now() > $expectedReturn,
                    'time_remaining' => $booking->status === 'confirmed' && !$booking->actual_return_time 
                        ? $this->calculateTimeRemaining($expectedReturn) 
                        : null,
                ];
            });

        // Active/Current Bookings (vehicles currently rented out)
        $activeBookings = Booking::whereIn('vehicle_id', $vehicleIds)
            ->where('status', 'confirmed')
            ->whereNull('actual_return_time')
            ->with([
                'user',
                'vehicle.make',
                'vehicle.type'
            ])
            ->get()
            ->map(function ($booking) {
                $expectedReturn = $booking->getCalculatedEndDatetimeAttribute();
                
                return [
                    'id' => $booking->id,
                    'vehicle_name' => ($booking->vehicle->make->name ?? 'Unknown') . ' ' . ($booking->vehicle->type->sub_type ?? 'Vehicle'),
                    'vehicle_image' => $booking->vehicle->main_photo_url ?? '/images/placeholder-vehicle.jpg',
                    'renter_name' => $booking->user->first_name . ' ' . $booking->user->last_name,
                    'renter_phone' => $booking->user->phone,
                    'expected_return' => $expectedReturn,
                    'is_overdue' => now() > $expectedReturn,
                    'time_remaining' => $this->calculateTimeRemaining($expectedReturn),
                    'has_overcharges' => $booking->has_overcharges,
                    'total_overcharges' => $booking->total_overcharges ?? 0,
                ];
            });

        // Earnings Statistics
        $earningsStats = $this->calculateEarningsStats($vehicleIds);

        // Booking Statistics
        $bookingStats = [
            'total_bookings' => Booking::whereIn('vehicle_id', $vehicleIds)->count(),
            'pending_bookings' => Booking::whereIn('vehicle_id', $vehicleIds)->where('status', 'pending')->count(),
            'active_bookings' => Booking::whereIn('vehicle_id', $vehicleIds)->where('status', 'confirmed')->whereNull('actual_return_time')->count(),
            'completed_bookings' => Booking::whereIn('vehicle_id', $vehicleIds)->where('status', 'completed')->count(),
            'cancelled_bookings' => Booking::whereIn('vehicle_id', $vehicleIds)->where('status', 'cancelled')->count(),
        ];

        // Most Popular Vehicle
        $mostPopularVehicle = $this->getMostPopularVehicle($vehicleIds);

        // Calendar Events (for the next 30 days)
        $calendarEvents = $this->getCalendarEvents($vehicleIds);

        // Vehicle Performance
        $vehiclePerformance = $this->getVehiclePerformance($vehicles);

        return Inertia::render('Owner/Dashboard', [
            'vehicleStats' => $vehicleStats,
            'recentBookings' => $recentBookings,
            'activeBookings' => $activeBookings,
            'earningsStats' => $earningsStats,
            'bookingStats' => $bookingStats,
            'mostPopularVehicle' => $mostPopularVehicle,
            'calendarEvents' => $calendarEvents,
            'vehiclePerformance' => $vehiclePerformance,
            'kycNotification' => $this->getKycNotification($user),
            'vehicles' => $vehicles->map(function ($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'name' => ($vehicle->make->name ?? 'Unknown') . ' ' . ($vehicle->type->sub_type ?? 'Vehicle'),
                    'image' => $vehicle->main_photo_url ?? '/images/placeholder-vehicle.jpg',
                    'is_available' => $vehicle->is_available,
                    'location' => $vehicle->location_name ?? 'Location not set',
                    'min_price' => $vehicle->pricingTiers->min('price') ?? 0,
                ];
            })
        ]);
    }

    private function getCurrentlyBookedCount($vehicleIds)
    {
        return Booking::whereIn('vehicle_id', $vehicleIds)
            ->where('status', 'confirmed')
            ->whereNull('actual_return_time')
            ->count();
    }

    private function calculateEarningsStats($vehicleIds)
    {
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        
        return [
            'total_earnings' => Booking::whereIn('vehicle_id', $vehicleIds)
                ->whereIn('status', ['confirmed', 'completed'])
                ->sum('total_amount'),
            'this_month_earnings' => Booking::whereIn('vehicle_id', $vehicleIds)
                ->whereIn('status', ['confirmed', 'completed'])
                ->where('created_at', '>=', $thisMonth)
                ->sum('total_amount'),
            'last_month_earnings' => Booking::whereIn('vehicle_id', $vehicleIds)
                ->whereIn('status', ['confirmed', 'completed'])
                ->whereBetween('created_at', [$lastMonth, $thisMonth])
                ->sum('total_amount'),
            'pending_payments' => Booking::whereIn('vehicle_id', $vehicleIds)
                ->whereHas('payment', function ($query) {
                    $query->whereNull('paid_at');
                })
                ->sum('total_amount'),
            'overcharge_earnings' => Booking::whereIn('vehicle_id', $vehicleIds)
                ->where('has_overcharges', true)
                ->sum('total_overcharges'),
        ];
    }

    private function getMostPopularVehicle($vehicleIds)
    {
        $popularVehicle = Booking::whereIn('vehicle_id', $vehicleIds)
            ->select('vehicle_id', DB::raw('COUNT(*) as booking_count'))
            ->groupBy('vehicle_id')
            ->orderBy('booking_count', 'desc')
            ->with(['vehicle.make', 'vehicle.type'])
            ->first();

        if ($popularVehicle) {
            return [
                'id' => $popularVehicle->vehicle->id,
                'name' => ($popularVehicle->vehicle->make->name ?? 'Unknown') . ' ' . ($popularVehicle->vehicle->type->sub_type ?? 'Vehicle'),
                'image' => $popularVehicle->vehicle->main_photo_url ?? '/images/placeholder-vehicle.jpg',
                'booking_count' => $popularVehicle->booking_count,
            ];
        }

        return null;
    }

    private function getCalendarEvents($vehicleIds)
    {
        $startDate = Carbon::now();
        $endDate = Carbon::now()->addDays(30);

        return Booking::whereIn('vehicle_id', $vehicleIds)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereBetween('pickup_datetime', [$startDate, $endDate])
            ->with(['vehicle.make', 'vehicle.type', 'user'])
            ->get()
            ->map(function ($booking) {
                $expectedReturn = $booking->getCalculatedEndDatetimeAttribute();
                
                return [
                    'id' => $booking->id,
                    'title' => ($booking->vehicle->make->name ?? 'Unknown') . ' ' . ($booking->vehicle->type->sub_type ?? 'Vehicle'),
                    'start' => $booking->pickup_datetime->toISOString(),
                    'end' => $expectedReturn->toISOString(),
                    'status' => $booking->status,
                    'renter' => $booking->user->first_name . ' ' . $booking->user->last_name,
                    'amount' => $booking->total_amount,
                ];
            });
    }

    private function getVehiclePerformance($vehicles)
    {
        return $vehicles->map(function ($vehicle) {
            $bookings = Booking::where('vehicle_id', $vehicle->id)->get();
            $totalEarnings = $bookings->whereIn('status', ['confirmed', 'completed'])->sum('total_amount');
            $totalBookings = $bookings->count();
            $completionRate = $totalBookings > 0 
                ? ($bookings->where('status', 'completed')->count() / $totalBookings) * 100 
                : 0;

            return [
                'id' => $vehicle->id,
                'name' => ($vehicle->make->name ?? 'Unknown') . ' ' . ($vehicle->type->sub_type ?? 'Vehicle'),
                'image' => $vehicle->main_photo_url ?? '/images/placeholder-vehicle.jpg',
                'total_bookings' => $totalBookings,
                'total_earnings' => $totalEarnings,
                'completion_rate' => round($completionRate, 1),
                'is_available' => $vehicle->is_available,
            ];
        })->sortByDesc('total_earnings')->values();
    }

    private function calculateTimeRemaining($expectedReturn)
    {
        $now = now();
        $diffInMinutes = $now->diffInMinutes($expectedReturn, false);
        
        if ($diffInMinutes <= 0) {
            // Overdue
            $overdue = abs($diffInMinutes);
            if ($overdue < 60) {
                return ['text' => $overdue . 'm overdue', 'overdue' => true];
            } else {
                $hours = floor($overdue / 60);
                $mins = $overdue % 60;
                return ['text' => $hours . 'h ' . $mins . 'm overdue', 'overdue' => true];
            }
        } else {
            // Time remaining
            if ($diffInMinutes < 60) {
                return ['text' => $diffInMinutes . 'm remaining', 'overdue' => false];
            } else {
                $hours = floor($diffInMinutes / 60);
                $mins = $diffInMinutes % 60;
                return ['text' => $hours . 'h ' . $mins . 'm remaining', 'overdue' => false];
            }
        }
    }
    
    private function getKycNotification($user)
    {
        if (!$user->license_submitted_at) {
            return [
                'type' => 'warning',
                'title' => 'Complete KYC Verification',
                'message' => 'Upload your driver\'s license to start listing vehicles.',
                'action' => 'Complete Verification',
                'url' => route('profile.edit'),
                'icon' => 'warning'
            ];
        }
        
        switch ($user->kyc_status) {
            case 'under_review':
                return [
                    'type' => 'info',
                    'title' => 'Verification Under Review',
                    'message' => 'Your driver\'s license is being reviewed. This usually takes 24-48 hours.',
                    'action' => 'View Status',
                    'url' => route('profile.edit'),
                    'icon' => 'clock'
                ];
                
            case 'approved':
                // Only show for recently approved (within last 7 days)
                if ($user->kyc_verified_at && $user->kyc_verified_at->diffInDays(now()) <= 7) {
                    return [
                        'type' => 'success',
                        'title' => 'Verification Approved!',
                        'message' => 'Your driver\'s license has been verified. You can now list vehicles.',
                        'action' => 'Add Vehicle',
                        'url' => route('owner.vehicles.create'),
                        'icon' => 'check'
                    ];
                }
                break;
                
            case 'rejected':
                return [
                    'type' => 'error',
                    'title' => 'Verification Rejected',
                    'message' => 'Your driver\'s license verification was rejected. Please resubmit with clear photos.',
                    'action' => 'Resubmit Documents',
                    'url' => route('profile.edit'),
                    'icon' => 'x',
                    'reason' => $user->kyc_rejection_reason
                ];
                
            default:
                return null;
        }
        
        return null;
    }
}
