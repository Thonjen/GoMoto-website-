<?php

namespace App\Http\Controllers;

use App\Models\{Overcharge, OverchargeType, Booking, Vehicle, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class OverchargeController extends Controller
{
    /**
     * Display overcharge settings for owner
     */
    public function index()
    {
        $user = Auth::user();
        $userVehicles = Vehicle::where('owner_id', $user->id)
            ->with(['bookings.overcharges.overchargeType'])
            ->get();
        
        return Inertia::render('Owner/Overcharge/Settings', [
            'user' => $user,
            'vehicles' => $userVehicles
        ]);
    }

    /**
     * Update user overcharge settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'enable_overcharges' => 'required|boolean',
            'late_return_rate' => 'required|numeric|min:0|max:9999.99',
            'out_of_city_base' => 'required|numeric|min:0|max:9999.99',
            'out_of_city_rate' => 'required|numeric|min:0|max:999.99',
            'grace_period_minutes' => 'required|integer|min:0|max:720',
            'overcharge_instructions' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        
        // Update the user's overcharge settings using the User model's update method
        User::where('id', $user->id)->update([
            'enable_overcharges' => $request->enable_overcharges,
            'late_return_rate' => $request->late_return_rate,
            'out_of_city_base' => $request->out_of_city_base,
            'out_of_city_rate' => $request->out_of_city_rate,
            'grace_period_minutes' => $request->grace_period_minutes,
            'overcharge_instructions' => $request->overcharge_instructions,
        ]);

        return back()->with('success', 'Overcharge settings updated successfully! Your new rates will apply to future bookings.');
    }

    /**
     * Show overcharge statistics for owner
     */
    public function stats()
    {
        $userId = Auth::id();
        
        // Get overcharges for owner's vehicles (paginated)
        $overcharges = Overcharge::whereHas('booking.vehicle', function ($query) use ($userId) {
            $query->where('owner_id', $userId);
        })->with(['booking.vehicle.brand', 'booking.vehicle.vehicleType', 'overchargeType'])
        ->latest()
        ->paginate(20);
        
        // Calculate statistics separately (not from paginated collection)
        $stats = [
            'total_overcharges' => Overcharge::whereHas('booking.vehicle', function ($query) use ($userId) {
                $query->where('owner_id', $userId);
            })->sum('amount'),
            'unpaid_overcharges' => Overcharge::whereHas('booking.vehicle', function ($query) use ($userId) {
                $query->where('owner_id', $userId);
            })->unpaid()->sum('amount'),
            'late_returns_count' => Overcharge::whereHas('booking.vehicle', function ($query) use ($userId) {
                $query->where('owner_id', $userId);
            })->whereHas('overchargeType', function ($query) {
                $query->where('name', 'late_return');
            })->count(),
            'out_of_city_count' => Overcharge::whereHas('booking.vehicle', function ($query) use ($userId) {
                $query->where('owner_id', $userId);
            })->whereHas('overchargeType', function ($query) {
                $query->where('name', 'out_of_city');
            })->count(),
        ];
        
        return Inertia::render('Owner/Overcharge/Stats', [
            'overcharges' => $overcharges,
            'stats' => $stats
        ]);
    }

    /**
     * Calculate and apply overcharges for a booking (called when vehicle is returned)
     */
    public function calculateOvercharges(Request $request, $bookingId)
    {
        $booking = Booking::with(['vehicle', 'overcharges'])->findOrFail($bookingId);
        
        // Check authorization
        if ($booking->vehicle->owner_id !== Auth::id() && $booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'return_latitude' => 'nullable|numeric',
            'return_longitude' => 'nullable|numeric',
            'return_location_name' => 'nullable|string'
        ]);

        // If booking is not completed yet, update return information
        if ($booking->status !== 'completed') {
            $booking->update([
                'status' => 'completed',
                'return_time' => now(),
                'return_latitude' => $request->return_latitude,
                'return_longitude' => $request->return_longitude,
                'return_location_name' => $request->return_location_name,
            ]);
        } else {
            // For completed bookings, clear existing overcharges and recalculate
            $booking->overcharges()->delete();
            $booking->update([
                'has_overcharges' => false,
                'total_overcharges' => 0
            ]);
        }

        // Calculate and apply overcharges
        $overchargeAmount = $booking->applyOvercharges();
        
        return response()->json([
            'success' => true,
            'overcharge_amount' => $overchargeAmount,
            'overcharges' => $booking->fresh()->overcharges->load('overchargeType'),
            'message' => $overchargeAmount > 0 
                ? "Overcharges calculated: ₱{$overchargeAmount}" 
                : 'No overcharges applied'
        ]);
    }

    /**
     * Mark overcharge as paid
     */
    public function markAsPaid($overchargeId)
    {
        $overcharge = Overcharge::with(['booking.vehicle'])->findOrFail($overchargeId);
        
        // Check authorization (only vehicle owner can mark as paid)
        if ($overcharge->booking->vehicle->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $overcharge->markAsPaid();
        
        return back()->with('success', 'Overcharge marked as paid');
    }

    /**
     * Extend booking to avoid overcharges
     */
    public function extendBooking(Request $request, $bookingId)
    {
        $booking = Booking::with(['vehicle', 'pricingTier'])->findOrFail($bookingId);
        
        // Check authorization (only the renter can extend their own booking)
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'extend_hours' => 'required|integer|min:1|max:72', // Max 3 days extension
        ]);

        $extendHours = $request->extend_hours;
        
        // Use extended_until if booking was previously extended, otherwise use calculated end time
        $currentEndTime = $booking->is_extended && $booking->extended_until 
            ? $booking->extended_until 
            : $booking->getCalculatedEndDatetimeAttribute();
            
        $newEndTime = $currentEndTime->copy()->addHours($extendHours);
        
        // Calculate additional cost (use current pricing tier rate)
        $hourlyRate = $booking->pricingTier->price; // Assuming price is per hour
        if ($booking->pricingTier->duration_unit === 'days') {
            $hourlyRate = $booking->pricingTier->price / 24; // Convert daily rate to hourly
        } elseif ($booking->pricingTier->duration_unit === 'minutes') {
            $hourlyRate = $booking->pricingTier->price * 60; // Convert per-minute to hourly
        }
        
        $additionalCost = $extendHours * $hourlyRate;
        
        // Extend the booking
        $booking->update([
            'is_extended' => true,
            'extended_until' => $newEndTime,
            'total_amount' => $booking->total_amount + $additionalCost
        ]);
        
        return response()->json([
            'success' => true,
            'new_end_time' => $newEndTime->format('Y-m-d H:i:s'),
            'additional_cost' => $additionalCost,
            'message' => "Booking extended by {$extendHours} hours for ₱{$additionalCost}"
        ]);
    }

    /**
     * Add manual overcharge to a booking
     */
    public function addManualOvercharge(Request $request, $bookingId)
    {
        $booking = Booking::with('vehicle')->findOrFail($bookingId);
        
        // Check authorization (only vehicle owner can add manual overcharges)
        if ($booking->vehicle->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'overcharge_type_id' => 'required|exists:overcharge_types,id',
            'amount' => 'required|numeric|min:0.01|max:99999.99',
            'details' => 'nullable|string|max:1000',
            'units' => 'nullable|numeric|min:0.01|max:999.99',
            'rate_applied' => 'nullable|numeric|min:0.01|max:99999.99',
            'occurred_at' => 'nullable|date',
        ]);

        // Create the manual overcharge
        $overcharge = Overcharge::create([
            'booking_id' => $booking->id,
            'overcharge_type_id' => $request->overcharge_type_id,
            'amount' => $request->amount,
            'details' => $request->details ?: 'Manual overcharge added by owner',
            'units' => $request->units ?: 1,
            'rate_applied' => $request->rate_applied ?: $request->amount,
            'is_paid' => false,
            'occurred_at' => $request->occurred_at ?: now(),
        ]);

        // Update booking totals
        $booking->update([
            'has_overcharges' => true,
            'total_overcharges' => $booking->overcharges()->sum('amount'),
        ]);

        return response()->json([
            'success' => true,
            'overcharge' => $overcharge->load('overchargeType'),
            'total_overcharges' => $booking->fresh()->total_overcharges,
            'message' => "Manual overcharge of ₱{$request->amount} added successfully"
        ]);
    }
}
