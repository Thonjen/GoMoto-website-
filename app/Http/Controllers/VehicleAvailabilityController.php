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
            'daysOfWeek' => VehicleAvailabilityBlock::getDaysOfWeek(),
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
            'block_type' => 'required|in:maintenance,personal_use,repairs,seasonal,time_restriction,other',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'is_recurring' => 'boolean',
            'recurring_type' => 'nullable|required_if:is_recurring,true|in:weekly,monthly,yearly,custom_days',
            'recurring_days' => 'nullable|array|min:1',
            'recurring_days.*' => 'integer|between:0,6',
            'recurring_end_date' => 'nullable|date|after:today',
            'is_time_based' => 'boolean',
            'affects_whole_day' => 'boolean',
            'start_time' => 'nullable|required_if:is_time_based,true|date_format:H:i',
            'end_time' => 'nullable|required_if:is_time_based,true|date_format:H:i|after:start_time',
        ]);

        $datesWithBookings = [];
        
        DB::transaction(function () use ($request, $vehicle, &$datesWithBookings) {
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
                    $datesWithBookings[] = $date;
                    continue; // Skip this date but continue with others
                }

                VehicleAvailabilityBlock::create([
                    'vehicle_id' => $vehicle->id,
                    'blocked_date' => $date,
                    'block_type' => $request->block_type,
                    'reason' => $request->reason,
                    'notes' => $request->notes,
                    'is_recurring' => $request->is_recurring ?? false,
                    'recurring_type' => $request->is_recurring ? $request->recurring_type : null,
                    'recurring_days' => $request->recurring_type === 'custom_days' ? $request->recurring_days : null,
                    'recurring_end_date' => $request->is_recurring ? $request->recurring_end_date : null,
                    'is_time_based' => $request->is_time_based ?? false,
                    'affects_whole_day' => $request->affects_whole_day ?? true,
                    'start_time' => $request->is_time_based ? $request->start_time : null,
                    'end_time' => $request->is_time_based ? $request->end_time : null,
                ]);
            }
        });
        
        // If some dates couldn't be blocked due to bookings, return validation error
        if (!empty($datesWithBookings)) {
            return back()->withErrors([
                'dates' => 'The following dates cannot be blocked due to existing bookings: ' . implode(', ', $datesWithBookings)
            ]);
        }

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
            'block_type' => 'required|in:maintenance,personal_use,repairs,seasonal,time_restriction,other',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'is_recurring' => 'boolean',
            'recurring_type' => 'nullable|required_if:is_recurring,true|in:weekly,monthly,yearly,custom_days',
            'recurring_days' => 'nullable|array|min:1',
            'recurring_days.*' => 'integer|between:0,6',
            'recurring_end_date' => 'nullable|date|after:today',
            'is_time_based' => 'boolean',
            'affects_whole_day' => 'boolean',
            'start_time' => 'nullable|required_if:is_time_based,true|date_format:H:i',
            'end_time' => 'nullable|required_if:is_time_based,true|date_format:H:i|after:start_time',
        ]);

        $block->update([
            'block_type' => $request->block_type,
            'reason' => $request->reason,
            'notes' => $request->notes,
            'is_recurring' => $request->is_recurring ?? false,
            'recurring_type' => $request->is_recurring ? $request->recurring_type : null,
            'recurring_days' => $request->recurring_type === 'custom_days' ? $request->recurring_days : null,
            'recurring_end_date' => $request->is_recurring ? $request->recurring_end_date : null,
            'is_time_based' => $request->is_time_based ?? false,
            'affects_whole_day' => $request->affects_whole_day ?? true,
            'start_time' => $request->is_time_based ? $request->start_time : null,
            'end_time' => $request->is_time_based ? $request->end_time : null,
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
            'block_type' => 'required|in:maintenance,personal_use,repairs,seasonal,time_restriction,other',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'is_time_based' => 'boolean',
            'affects_whole_day' => 'boolean',
            'start_time' => 'nullable|required_if:is_time_based,true|date_format:H:i',
            'end_time' => 'nullable|required_if:is_time_based,true|date_format:H:i|after:start_time',
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
                    'is_time_based' => $request->is_time_based ?? false,
                    'affects_whole_day' => $request->affects_whole_day ?? true,
                    'start_time' => $request->is_time_based ? $request->start_time : null,
                    'end_time' => $request->is_time_based ? $request->end_time : null,
                ]);
            }
        });

        return back()->with('success', 'Date range blocked successfully.');
    }

    /**
     * Create recurring blocks for specific days
     */
    public function storeRecurringDays(Request $request, Vehicle $vehicle)
    {
        // Check ownership
        if ($vehicle->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'recurring_days' => 'required|array|min:1',
            'recurring_days.*' => 'integer|between:0,6',
            'block_type' => 'required|in:maintenance,personal_use,repairs,seasonal,time_restriction,other',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'is_time_based' => 'boolean',
            'affects_whole_day' => 'boolean',
            'start_time' => 'nullable|required_if:is_time_based,true|date_format:H:i',
            'end_time' => 'nullable|required_if:is_time_based,true|date_format:H:i|after:start_time',
        ]);

        // Create a single recurring block that will apply to the specified days
        VehicleAvailabilityBlock::create([
            'vehicle_id' => $vehicle->id,
            'blocked_date' => $request->start_date,
            'block_type' => $request->block_type,
            'reason' => $request->reason,
            'notes' => $request->notes,
            'is_recurring' => true,
            'recurring_type' => 'custom_days',
            'recurring_days' => $request->recurring_days,
            'recurring_end_date' => $request->end_date,
            'is_time_based' => $request->is_time_based ?? false,
            'affects_whole_day' => $request->affects_whole_day ?? true,
            'start_time' => $request->is_time_based ? $request->start_time : null,
            'end_time' => $request->is_time_based ? $request->end_time : null,
        ]);

        return back()->with('success', 'Recurring availability block created successfully.');
    }

    /**
     * Check if vehicle has bookings on a specific date
     */
    private function hasBookingsOnDate($vehicleId, $date)
    {
        $checkDate = Carbon::parse($date);
        
        // Check if there are any ACTIVE bookings (pending or confirmed) that might overlap with this date
        // Completed and cancelled bookings should not block future availability
        return DB::table('bookings')
            ->join('vehicle_pricing_tiers', 'bookings.pricing_tier_id', '=', 'vehicle_pricing_tiers.id')
            ->where('bookings.vehicle_id', $vehicleId)
            ->whereIn('bookings.status', ['pending', 'confirmed'])
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
            ->whereIn('bookings.status', ['pending', 'confirmed'])
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

    /**
     * Delete multiple availability blocks
     */
    public function batchDestroy(Request $request, Vehicle $vehicle)
    {
        // Check ownership
        if ($vehicle->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'block_ids' => 'required|array|min:1',
            'block_ids.*' => 'required|integer|exists:vehicle_availability_blocks,id'
        ]);

        // Delete blocks that belong to this vehicle
        $deletedCount = VehicleAvailabilityBlock::where('vehicle_id', $vehicle->id)
            ->whereIn('id', $request->block_ids)
            ->delete();

        return back()->with('success', "Successfully deleted {$deletedCount} availability block(s).");
    }
}
