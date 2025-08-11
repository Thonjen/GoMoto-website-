<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Redirect owners to owner dashboard
        if ($user->role && $user->role->name === 'owner') {
            return redirect()->route('owner.dashboard');
        }
        
        // Regular user dashboard continues here
        
        // Get user's recent bookings (last 5)
        $recentBookings = Booking::where('user_id', $user->id)
            ->with([
                'vehicle.make',
                'vehicle.type', 
                'vehicle.owner',
                'pricingTier',
                'payment.paymentMode',
                'overcharges.overchargeType'
            ])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($booking) {
                $expectedReturn = $booking->getCalculatedEndDatetimeAttribute();
                
                return [
                    'id' => $booking->id,
                    'vehicle_name' => ($booking->vehicle->make->name ?? 'Unknown') . ' ' . ($booking->vehicle->type->sub_type ?? 'Vehicle'),
                    'vehicle_image' => $booking->vehicle->main_photo_url ?? '/images/placeholder-vehicle.jpg',
                    'pickup_datetime' => $booking->pickup_datetime,
                    'expected_return' => $expectedReturn,
                    'status' => $booking->status,
                    'total_amount' => $booking->total_amount,
                    'owner_name' => $booking->vehicle->owner->first_name . ' ' . $booking->vehicle->owner->last_name,
                    'owner_phone' => $booking->vehicle->owner->phone,
                    'payment_status' => $booking->payment?->paid_at ? 'paid' : 'pending',
                    'has_overcharges' => $booking->has_overcharges,
                    'total_overcharges' => $booking->total_overcharges ?? 0,
                    'is_overdue' => $booking->status === 'confirmed' && !$booking->actual_return_time && now() > $expectedReturn,
                    'can_extend' => $booking->status === 'confirmed' && !$booking->actual_return_time,
                ];
            });

        // Get active booking (currently rented vehicle)
        $activeBooking = Booking::where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->whereNull('actual_return_time')
            ->with([
                'vehicle.make',
                'vehicle.type',
                'vehicle.owner',
                'pricingTier'
            ])
            ->first();

        if ($activeBooking) {
            $expectedReturn = $activeBooking->getCalculatedEndDatetimeAttribute();
            $activeBooking = [
                'id' => $activeBooking->id,
                'vehicle_name' => ($activeBooking->vehicle->make->name ?? 'Unknown') . ' ' . ($activeBooking->vehicle->type->sub_type ?? 'Vehicle'),
                'vehicle_image' => $activeBooking->vehicle->main_photo_url ?? '/images/placeholder-vehicle.jpg',
                'pickup_datetime' => $activeBooking->pickup_datetime,
                'expected_return' => $expectedReturn,
                'owner_name' => $activeBooking->vehicle->owner->first_name . ' ' . $activeBooking->vehicle->owner->last_name,
                'owner_phone' => $activeBooking->vehicle->owner->phone,
                'is_overdue' => now() > $expectedReturn,
                'time_remaining' => $this->calculateTimeRemaining($expectedReturn),
            ];
        }

        // Get booking statistics
        $bookingStats = [
            'total_bookings' => Booking::where('user_id', $user->id)->count(),
            'completed_bookings' => Booking::where('user_id', $user->id)->where('status', 'completed')->count(),
            'cancelled_bookings' => Booking::where('user_id', $user->id)->where('status', 'cancelled')->count(),
            'total_spent' => Booking::where('user_id', $user->id)->whereIn('status', ['confirmed', 'completed'])->sum('total_amount'),
            'unpaid_overcharges' => Booking::where('user_id', $user->id)
                ->whereHas('overcharges', function ($query) {
                    $query->where('is_paid', false);
                })
                ->sum('total_overcharges'),
        ];

        // Get featured/recommended vehicles (vehicles with high ratings or popular ones)
        $featuredVehicles = Vehicle::where('is_available', true)
            ->with(['make', 'type', 'pricingTiers'])
            ->inRandomOrder()
            ->limit(6)
            ->get()
            ->map(function ($vehicle) {
                $minPrice = $vehicle->pricingTiers->min('price') ?? 0;
                
                return [
                    'id' => $vehicle->id,
                    'name' => ($vehicle->make->name ?? 'Unknown') . ' ' . ($vehicle->type->sub_type ?? 'Vehicle'),
                    'image' => $vehicle->main_photo_url ?? '/images/placeholder-vehicle.jpg',
                    'location' => $vehicle->location_name ?? 'Location not set',
                    'price_from' => $minPrice,
                    'year' => $vehicle->year,
                ];
            });

        return Inertia::render('Dashboard', [
            'recentBookings' => $recentBookings,
            'activeBooking' => $activeBooking,
            'bookingStats' => $bookingStats,
            'featuredVehicles' => $featuredVehicles,
        ]);
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
}
