<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleAvailabilityBlock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class VehicleAvailabilityController extends Controller
{
    /**
     * Display availability calendar for a vehicle
     */
    public function show(Vehicle $vehicle)
    {
        // Check ownership
        if ($vehicle->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Get current month and surrounding months for calendar
        $startDate = now()->startOfMonth()->subMonth();
        $endDate = now()->endOfMonth()->addMonths(2);

        // Get availability blocks for this period
        $blocks = $vehicle->availabilityBlocks()
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('blocked_date', [$startDate, $endDate])
                      ->orWhere(function ($q) use ($endDate) {
                          $q->where('is_recurring', true)
                            ->where(function ($dateQ) use ($endDate) {
                                $dateQ->whereNull('recurring_end_date')
                                      ->orWhere('recurring_end_date', '>=', $endDate);
                            });
                      });
            })
            ->orderBy('blocked_date')
            ->get();

        // Get existing bookings for this period
        // For now, let's simplify to avoid complex SQL - we'll calculate end times in PHP
        $bookings = $vehicle->bookings()
            ->join('vehicle_pricing_tiers', 'bookings.pricing_tier_id', '=', 'vehicle_pricing_tiers.id')
            ->where('bookings.status', '!=', 'cancelled')
            ->whereBetween('bookings.pickup_datetime', [$startDate, $endDate])
            ->select('bookings.*')
            ->with(['pricingTier', 'user'])
            ->get();

        return Inertia::render('Owner/Vehicle/Availability', [
            'vehicle' => $vehicle->load(['make', 'model']),
            'blocks' => $blocks,
            'bookings' => $bookings,
            'blockTypes' => VehicleAvailabilityBlock::getBlockTypes(),
            'recurringTypes' => VehicleAvailabilityBlock::getRecurringTypes(),
        ]);
    }

    /**
     * Store a new availability block
     */
    public function store(Request $request, Vehicle $vehicle)
    {
        // Check ownership
        if ($vehicle->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'dates' => 'required|array|min:1',
            'dates.*' => 'required|date|after_or_equal:today',
            'block_type' => 'required|in:maintenance,personal_use,repairs,seasonal,other',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'is_recurring' => 'boolean',
            'recurring_type' => 'nullable|required_if:is_recurring,true|in:weekly,monthly,yearly',
            'recurring_end_date' => 'nullable|date|after:today',
        ]);

        DB::transaction(function () use ($request, $vehicle) {
            foreach ($request->dates as $date) {
                // Check if date is already blocked
                $existingBlock = VehicleAvailabilityBlock::where('vehicle_id', $vehicle->id)
                    ->where('blocked_date', $date)
                    ->first();

                if ($existingBlock) {
                    continue; // Skip if already blocked
                }

                // Check if there are existing bookings for this date
                $hasBookings = $this->hasBookingsOnDate($vehicle->id, $date);
                if ($hasBookings) {
                    throw new \Exception("Cannot block {$date} - there are existing bookings for this date.");
                }

                VehicleAvailabilityBlock::create([
                    'vehicle_id' => $vehicle->id,
                    'blocked_date' => $date,
                    'block_type' => $request->block_type,
                    'reason' => $request->reason,
                    'notes' => $request->notes,
                    'is_recurring' => $request->is_recurring ?? false,
                    'recurring_type' => $request->is_recurring ? $request->recurring_type : null,
                    'recurring_end_date' => $request->is_recurring ? $request->recurring_end_date : null,
                ]);
            }
        });

        return back()->with('success', 'Availability blocks created successfully.');
    }

    /**
     * Update an existing availability block
     */
    public function update(Request $request, Vehicle $vehicle, VehicleAvailabilityBlock $block)
    {
        // Check ownership
        if ($vehicle->owner_id !== Auth::id() || $block->vehicle_id !== $vehicle->id) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'block_type' => 'required|in:maintenance,personal_use,repairs,seasonal,other',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'is_recurring' => 'boolean',
            'recurring_type' => 'nullable|required_if:is_recurring,true|in:weekly,monthly,yearly',
            'recurring_end_date' => 'nullable|date|after:today',
        ]);

        $block->update([
            'block_type' => $request->block_type,
            'reason' => $request->reason,
            'notes' => $request->notes,
            'is_recurring' => $request->is_recurring ?? false,
            'recurring_type' => $request->is_recurring ? $request->recurring_type : null,
            'recurring_end_date' => $request->is_recurring ? $request->recurring_end_date : null,
        ]);

        return back()->with('success', 'Availability block updated successfully.');
    }

    /**
     * Remove an availability block
     */
    public function destroy(Vehicle $vehicle, VehicleAvailabilityBlock $block)
    {
        // Check ownership
        if ($vehicle->owner_id !== Auth::id() || $block->vehicle_id !== $vehicle->id) {
            abort(403, 'Unauthorized');
        }

        $block->delete();

        return back()->with('success', 'Availability block removed successfully.');
    }

    /**
     * Get availability data for a date range (API endpoint)
     */
    public function getAvailabilityData(Request $request, Vehicle $vehicle)
    {
        // Check ownership
        if ($vehicle->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $startDate = $request->start_date;
        $endDate = $request->end_date;

        // Get blocked dates
        $blockedDates = VehicleAvailabilityBlock::getBlockedDatesInRange(
            $vehicle->id, 
            $startDate, 
            $endDate
        );

        // Get booking dates
        $bookingDates = $this->getBookingDatesInRange($vehicle->id, $startDate, $endDate);

        return response()->json([
            'blocked_dates' => $blockedDates,
            'booking_dates' => $bookingDates,
        ]);
    }

    /**
     * Bulk create availability blocks for date range
     */
    public function bulkStore(Request $request, Vehicle $vehicle)
    {
        // Check ownership
        if ($vehicle->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'block_type' => 'required|in:maintenance,personal_use,repairs,seasonal,other',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $dates = [];

        $current = $startDate->copy();
        while ($current->lte($endDate)) {
            $dates[] = $current->toDateString();
            $current->addDay();
        }

        DB::transaction(function () use ($dates, $request, $vehicle) {
            foreach ($dates as $date) {
                // Check if date is already blocked
                $existingBlock = VehicleAvailabilityBlock::where('vehicle_id', $vehicle->id)
                    ->where('blocked_date', $date)
                    ->first();

                if ($existingBlock) {
                    continue; // Skip if already blocked
                }

                // Check if there are existing bookings for this date
                $hasBookings = $this->hasBookingsOnDate($vehicle->id, $date);
                if ($hasBookings) {
                    throw new \Exception("Cannot block {$date} - there are existing bookings for this date.");
                }

                VehicleAvailabilityBlock::create([
                    'vehicle_id' => $vehicle->id,
                    'blocked_date' => $date,
                    'block_type' => $request->block_type,
                    'reason' => $request->reason,
                    'notes' => $request->notes,
                    'is_recurring' => false,
                ]);
            }
        });

        return back()->with('success', 'Date range blocked successfully.');
    }

    /**
     * Check if vehicle has bookings on a specific date
     */
    private function hasBookingsOnDate($vehicleId, $date)
    {
        $checkDate = Carbon::parse($date);
        
        // Check if there are any bookings that might overlap with this date
        // For now, simplified query - we'll add proper end time checking later
        return DB::table('bookings')
            ->join('vehicle_pricing_tiers', 'bookings.pricing_tier_id', '=', 'vehicle_pricing_tiers.id')
            ->where('bookings.vehicle_id', $vehicleId)
            ->where('bookings.status', '!=', 'cancelled')
            ->whereDate('bookings.pickup_datetime', '<=', $checkDate)
            ->exists();
    }

    /**
     * Get booking dates in a range
     */
    private function getBookingDatesInRange($vehicleId, $startDate, $endDate)
    {
        $bookings = DB::table('bookings')
            ->join('vehicle_pricing_tiers', 'bookings.pricing_tier_id', '=', 'vehicle_pricing_tiers.id')
            ->where('bookings.vehicle_id', $vehicleId)
            ->where('bookings.status', '!=', 'cancelled')
            ->whereBetween('pickup_datetime', [$startDate, $endDate])
            ->select('pickup_datetime', 'vehicle_pricing_tiers.duration_from', 'vehicle_pricing_tiers.duration_unit')
            ->get();

        $bookedDates = [];
        foreach ($bookings as $booking) {
            $start = Carbon::parse($booking->pickup_datetime);
            $end = $start->copy();
            
            switch ($booking->duration_unit) {
                case 'hours':
                    $end->addHours($booking->duration_from);
                    break;
                case 'days':
                    $end->addDays($booking->duration_from);
                    break;
                case 'weeks':
                    $end->addWeeks($booking->duration_from);
                    break;
            }

            $current = $start->copy();
            while ($current->lt($end)) {
                $bookedDates[] = $current->toDateString();
                $current->addDay();
            }
        }

        return array_unique($bookedDates);
    }
}
