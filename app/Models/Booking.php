<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use App\Models\Overcharge;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'vehicle_id',
        'pricing_tier_id',
        'pickup_datetime',
        'status',
        'total_amount',
        'actual_return_time',
        'pickup_latitude',
        'pickup_longitude',
        'return_latitude',
        'return_longitude',
        'pickup_location_name',
        'return_location_name',
        'has_overcharges',
        'total_overcharges',
        'is_extended',
        'extended_until',
    ];

    protected $casts = [
        'pickup_datetime' => 'datetime',
        'total_amount' => 'decimal:2',
        'actual_return_time' => 'datetime',
        'pickup_latitude' => 'decimal:8',
        'pickup_longitude' => 'decimal:8',
        'return_latitude' => 'decimal:8',
        'return_longitude' => 'decimal:8',
        'has_overcharges' => 'boolean',
        'total_overcharges' => 'decimal:2',
        'is_extended' => 'boolean',
        'extended_until' => 'datetime',
    ];

    /**
     * SECURITY: Prevent client-side time manipulation for actual_return_time
     * Always use server time when setting return time
     */
    public function setActualReturnTimeAttribute($value)
    {
        // If someone tries to set actual_return_time manually, always use server time instead
        // This prevents any possibility of client-side time manipulation
        if ($value !== null) {
            $this->attributes['actual_return_time'] = \Carbon\Carbon::now('UTC');
        } else {
            $this->attributes['actual_return_time'] = null;
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function pricingTier()
    {
        return $this->belongsTo(\App\Models\VehiclePricingTier::class, 'pricing_tier_id');
    }

    public function overcharges()
    {
        return $this->hasMany(Overcharge::class);
    }

    public function extensionRequests()
    {
        return $this->hasMany(BookingExtensionRequest::class);
    }

    public function rating()
    {
        return $this->hasOne(VehicleRating::class);
    }

    public function pendingExtensionRequest()
    {
        return $this->hasOne(BookingExtensionRequest::class)->where('status', 'pending');
    }

    public function getDurationInDaysAttribute()
    {
        if (!$this->pricingTier) {
            return 1;
        }
        
        $tier = $this->pricingTier;
        switch ($tier->duration_unit) {
            case 'minutes':
                return max(1, ceil($tier->duration_from / 1440)); // 1440 minutes in a day
            case 'hours':
                return max(1, ceil($tier->duration_from / 24)); // 24 hours in a day
            case 'days':
                return $tier->duration_from;
            default:
                return 1;
        }
    }

    /**
     * Calculate the end datetime based on pickup datetime and pricing tier duration
     * SECURITY: For completed bookings, always use stored calculated_end_datetime to prevent tampering
     */
    public function getCalculatedEndDatetimeAttribute()
    {
        // If booking has been extended, return the extended time
        if ($this->is_extended && $this->extended_until) {
            return \Carbon\Carbon::parse($this->extended_until);
        }

        // SECURITY FIX: For completed bookings, ALWAYS use the stored calculated_end_datetime
        // This prevents any possibility of client-side time manipulation affecting overcharge calculations
        if ($this->status === 'completed') {
            // Force refresh from database to get actual stored value
            $freshData = DB::table('bookings')->where('id', $this->id)->first();
            if ($freshData && $freshData->calculated_end_datetime) {
                return \Carbon\Carbon::parse($freshData->calculated_end_datetime);
            }
        }

        // Fallback: recalculate from pickup_datetime and pricing tier (for non-completed bookings)
        if (!$this->pickup_datetime || !$this->pricingTier) {
            return null;
        }

        $tier = $this->pricingTier;
        $pickup = \Carbon\Carbon::parse($this->pickup_datetime); // Create a copy to avoid modifying original

        switch ($tier->duration_unit) {
            case 'minutes':
                return $pickup->addMinutes($tier->duration_from);
            case 'hours':
                return $pickup->addHours($tier->duration_from);
            case 'days':
                return $pickup->addDays($tier->duration_from);
            default:
                return $pickup->addDays(1);
        }
    }

    /**
     * Check if this booking conflicts with existing bookings for the same vehicle
     */
    public static function hasConflict($vehicleId, $pickupDatetime, $pricingTier, $excludeBookingId = null)
    {
        // Calculate end datetime for the new booking
        $endDatetime = self::calculateEndDatetime($pickupDatetime, $pricingTier);

        // Check for availability blocks first
        if (\App\Models\VehicleAvailabilityBlock::hasBlockedDatesInRange(
            $vehicleId, 
            \Carbon\Carbon::parse($pickupDatetime)->toDateString(), 
            \Carbon\Carbon::parse($endDatetime)->toDateString()
        )) {
            return true; // Conflict with availability block
        }

        // Get all active bookings for this vehicle
        $existingBookings = self::where('vehicle_id', $vehicleId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->when($excludeBookingId, function ($query) use ($excludeBookingId) {
                return $query->where('id', '!=', $excludeBookingId);
            })
            ->with('pricingTier')
            ->get();

        // Check each existing booking for overlap
        foreach ($existingBookings as $booking) {
            $existingStart = $booking->pickup_datetime;
            $existingEnd = self::calculateEndDatetime($existingStart, $booking->pricingTier);

            // Check for overlap: bookings overlap if one starts before the other ends
            if ($pickupDatetime < $existingEnd && $endDatetime > $existingStart) {
                return true; // Conflict found
            }
        }

        return false; // No conflicts
    }

    /**
     * Check if user already has an active booking for this vehicle during overlapping time
     */
    public static function userHasActiveBooking($userId, $vehicleId, $pickupDatetime, $pricingTier, $excludeBookingId = null)
    {
        // Calculate end datetime for the new booking
        $endDatetime = self::calculateEndDatetime($pickupDatetime, $pricingTier);

        // Get all active bookings for this user and vehicle
        $userBookings = self::where('user_id', $userId)
            ->where('vehicle_id', $vehicleId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->when($excludeBookingId, function ($query) use ($excludeBookingId) {
                return $query->where('id', '!=', $excludeBookingId);
            })
            ->with('pricingTier')
            ->get();

        // Check each existing booking for overlap
        foreach ($userBookings as $booking) {
            $existingStart = $booking->pickup_datetime;
            $existingEnd = self::calculateEndDatetime($existingStart, $booking->pricingTier);

            // Check for overlap: bookings overlap if one starts before the other ends
            if ($pickupDatetime < $existingEnd && $endDatetime > $existingStart) {
                return $booking; // Return the conflicting booking
            }
        }

        return null; // No user conflicts
    }

    /**
     * Get user's active bookings for a specific vehicle
     */
    public static function getUserActiveBookingsForVehicle($userId, $vehicleId)
    {
        return self::where('user_id', $userId)
            ->where('vehicle_id', $vehicleId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->with(['pricingTier'])
            ->orderBy('pickup_datetime')
            ->get();
    }

    /**
     * Static method to calculate end datetime
     */
    public static function calculateEndDatetime($pickupDatetime, $pricingTier)
    {
        $pickup = \Carbon\Carbon::parse($pickupDatetime);

        switch ($pricingTier->duration_unit) {
            case 'minutes':
                return $pickup->copy()->addMinutes($pricingTier->duration_from);
            case 'hours':
                return $pickup->copy()->addHours($pricingTier->duration_from);
            case 'days':
                return $pickup->copy()->addDays($pricingTier->duration_from);
            default:
                return $pickup->copy()->addDays(1);
        }
    }

    /**
     * Update the calculated_end_datetime field when booking is saved
     */
    protected static function booted()
    {
        static::saving(function ($booking) {
            if ($booking->pickup_datetime && $booking->pricingTier) {
                $booking->calculated_end_datetime = self::calculateEndDatetime(
                    $booking->pickup_datetime,
                    $booking->pricingTier
                );
            }
        });
    }

    /**
     * Check if booking is late (past expected return time + grace period)
     */
    public function isLate()
    {
        $owner = $this->vehicle->owner;
        $gracePeriodMinutes = $owner->grace_period_minutes ?? 30;
        
        if (!$this->actual_return_time) {
            // If not returned yet, check if current server time is past expected return + grace period
            $expectedReturn = $this->getCalculatedEndDatetimeAttribute();
            if (!$expectedReturn) return false;
            
            $graceEndTime = $expectedReturn->copy()->addMinutes($gracePeriodMinutes);
            // Use server time from database instead of client time
            $serverTime = \Carbon\Carbon::now('UTC');
            return $serverTime > $graceEndTime;
        }
        
        // If returned, check if return was late (past expected + grace)
        $expectedReturn = $this->getCalculatedEndDatetimeAttribute();
        if (!$expectedReturn) return false;
        
        $graceEndTime = $expectedReturn->copy()->addMinutes($gracePeriodMinutes);
        return $this->actual_return_time > $graceEndTime;
    }

    /**
     * Get hours late for return (after grace period)
     */
    public function getHoursLate()
    {
        $expectedReturn = $this->getCalculatedEndDatetimeAttribute();
        if (!$expectedReturn) return 0;

        $owner = $this->vehicle->owner;
        $gracePeriodMinutes = $owner->grace_period_minutes ?? 30;
        $graceEndTime = $expectedReturn->copy()->addMinutes($gracePeriodMinutes);

        // SECURITY: Only use stored actual_return_time, never client time
        if (!$this->actual_return_time) {
            // If booking not yet returned, use server time for calculation
            $compareTime = \Carbon\Carbon::now('UTC');
        } else {
            // Use the stored return time from when vehicle was actually returned
            $compareTime = $this->actual_return_time;
        }
        
        if ($compareTime <= $graceEndTime) return 0;
        
        // Fixed: Calculate hours from grace end time to actual return time
        return $graceEndTime->diffInHours($compareTime);
    }

    /**
     * Check if return location is outside city limits
     */
    public function isOutOfCity()
    {
        if (!$this->return_latitude || !$this->return_longitude) {
            return false;
        }

        // Use Surigao City geojson to check if point is inside city limits
        return !$this->isPointInsideSurigaoCity($this->return_latitude, $this->return_longitude);
    }

    /**
     * Calculate distance from city center (rough estimate)
     */
    public function getDistanceFromCityCenter()
    {
        if (!$this->return_latitude || !$this->return_longitude) {
            return 0;
        }

        // Surigao City center coordinates
        $cityLat = 9.7788;
        $cityLng = 125.4984;

        return $this->calculateDistance($cityLat, $cityLng, $this->return_latitude, $this->return_longitude);
    }

    /**
     * Format hours into user-friendly time display
     * Examples: 0.5 hours → "30 minutes", 1.0 hours → "1 hour", 2.5 hours → "2 hours 30 minutes"
     */
    private function formatTimeDisplay($hours)
    {
        if ($hours < 1) {
            // Less than 1 hour, show in minutes
            $minutes = round($hours * 60);
            return $minutes === 1 ? "1 minute" : "{$minutes} minutes";
        } else {
            // 1 hour or more
            $wholeHours = floor($hours);
            $remainingMinutes = round(($hours - $wholeHours) * 60);
            
            $hourText = $wholeHours === 1 ? "1 hour" : "{$wholeHours} hours";
            
            if ($remainingMinutes > 0) {
                $minuteText = $remainingMinutes === 1 ? "1 minute" : "{$remainingMinutes} minutes";
                return "{$hourText} {$minuteText}";
            } else {
                return $hourText;
            }
        }
    }

    /**
     * Calculate overcharges for this booking using owner's settings
     * SECURITY WARNING: This method must never use client-side time (now(), request time, etc.)
     * All time calculations should use stored server timestamps to prevent manipulation
     */
    public function calculateOvercharges()
    {
        $overcharges = [];
        $owner = $this->vehicle->owner;

        // Skip if owner has overcharges disabled
        if (!$owner->enable_overcharges) {
            return $overcharges;
        }

        // Check for late return
        if ($this->isLate()) {
            $lateHours = $this->getHoursLate();
            if ($lateHours > 0) {
                $rate = $owner->late_return_rate ?? 100.00;
                $amount = $lateHours * $rate;
                $timeDisplay = $this->formatTimeDisplay($lateHours);
                $overcharges[] = [
                    'overcharge_type_id' => 1, // late_return type ID
                    'amount' => $amount,
                    'units' => $lateHours,
                    'rate_applied' => $rate,
                    'details' => "Late return by {$timeDisplay} (after {$owner->grace_period_minutes} minute grace period)",
                    // SECURITY: Use stored return time or server time, never client time
                    'occurred_at' => $this->actual_return_time ?? \Carbon\Carbon::now('UTC')
                ];
            }
        }

        // Check for out of city return
        if ($this->isOutOfCity()) {
            $distance = $this->getDistanceFromCityCenter();
            if ($distance > 0) {
                $baseRate = $owner->out_of_city_base ?? 500.00;
                $kmRate = $owner->out_of_city_rate ?? 50.00;
                $amount = $baseRate + ($distance * $kmRate);
                $overcharges[] = [
                    'overcharge_type_id' => 2, // out_of_city type ID
                    'amount' => $amount,
                    'units' => round($distance, 2),
                    'rate_applied' => $kmRate,
                    'details' => "Out of city return: ₱{$baseRate} base + {$distance}km × ₱{$kmRate}",
                    // SECURITY: Use stored return time or server time, never client time
                    'occurred_at' => $this->actual_return_time ?? \Carbon\Carbon::now('UTC')
                ];
            }
        }

        return $overcharges;
    }

    /**
     * Apply overcharges to this booking
     */
    public function applyOvercharges()
    {
        $overchargeData = $this->calculateOvercharges();
        $totalOvercharges = 0;

        foreach ($overchargeData as $overcharge) {
            $overcharge['booking_id'] = $this->id;
            Overcharge::create($overcharge);
            $totalOvercharges += $overcharge['amount'];
        }

        if ($totalOvercharges > 0) {
            $this->update([
                'has_overcharges' => true,
                'total_overcharges' => $totalOvercharges
            ]);
        }

        return $totalOvercharges;
    }

    /**
     * Update overcharge status based on current overcharges
     */
    public function updateOverchargeStatus()
    {
        $totalOvercharges = $this->overcharges()->sum('amount');
        $this->update([
            'has_overcharges' => $totalOvercharges > 0,
            'total_overcharges' => $totalOvercharges
        ]);
    }

    /**
     * Extend booking until specified time
     */
    public function extendUntil($newEndTime, $additionalCost = 0)
    {
        $this->update([
            'is_extended' => true,
            'extended_until' => $newEndTime,
            'total_amount' => $this->total_amount + $additionalCost
        ]);
    }

    /**
     * Check if point is inside Surigao City limits using simplified polygon check
     */
    private function isPointInsideSurigaoCity($lat, $lng)
    {
        // Simplified bounding box for Surigao City
        // In a real implementation, you would use the actual geojson polygon
        $bounds = [
            'north' => 9.8200,
            'south' => 9.7300,
            'east' => 125.5200,
            'west' => 125.4700
        ];

        return $lat >= $bounds['south'] && $lat <= $bounds['north'] &&
               $lng >= $bounds['west'] && $lng <= $bounds['east'];
    }

    /**
     * Calculate distance between two points using Haversine formula
     */
    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng/2) * sin($dLng/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c;
    }

    /**
     * Apply extension from an approved extension request
     */
    public function applyExtension($hours, $additionalCost)
    {
        $currentEndTime = $this->is_extended && $this->extended_until 
            ? $this->extended_until 
            : $this->getCalculatedEndDatetimeAttribute();
            
        $newEndTime = $currentEndTime->copy()->addHours($hours);
        
        $this->extendUntil($newEndTime, $additionalCost);
    }

    /**
     * Check if this booking can be extended
     */
    public function canBeExtended()
    {
        return $this->status === 'confirmed' 
            && !$this->actual_return_time 
            && $this->vehicle->allow_extensions
            && !$this->pendingExtensionRequest;
    }

    /**
     * Securely mark booking as returned using server time
     * This prevents client-side time manipulation
     */
    public function markAsReturned($latitude = null, $longitude = null, $locationName = null)
    {
        $this->update([
            'status' => 'completed',
            'actual_return_time' => \Carbon\Carbon::now('UTC'), // Always use server time
            'return_latitude' => $latitude,
            'return_longitude' => $longitude,
            'return_location_name' => $locationName,
        ]);
        
        return $this;
    }

    /**
     * Calculate extension cost for a given number of hours
     */
    public function calculateExtensionCost($hours)
    {
        if (!$this->pricingTier || $hours <= 0) {
            return 0;
        }

        $hourlyRate = $this->pricingTier->price;
        
        // Convert rate to hourly if needed
        if ($this->pricingTier->duration_unit === 'days') {
            $hourlyRate = $this->pricingTier->price / 24;
        } elseif ($this->pricingTier->duration_unit === 'minutes') {
            $hourlyRate = $this->pricingTier->price * 60;
        }
        
        return $hours * $hourlyRate;
    }

    /**
     * Check if booking is completed and eligible for rating
     */
    public function isEligibleForRating()
    {
        return $this->status === 'completed' && 
               $this->actual_return_time !== null && 
               !$this->rating()->exists();
    }

    /**
     * Check if booking has been rated
     */
    public function hasBeenRated()
    {
        return $this->rating()->exists();
    }

    /**
     * Get time since booking completion (for determining when to prompt for rating)
     */
    public function getHoursSinceCompletion()
    {
        if (!$this->actual_return_time) {
            return null;
        }
        
        return $this->actual_return_time->diffInHours(now());
    }

    /**
     * Check if booking should show rating prompt (completed but not rated, within reasonable time)
     */
    public function shouldPromptForRating()
    {
        if (!$this->isEligibleForRating()) {
            return false;
        }
        
        $hoursSinceCompletion = $this->getHoursSinceCompletion();
        
        // Prompt if completed within last 7 days (168 hours)
        return $hoursSinceCompletion !== null && $hoursSinceCompletion <= 168;
    }
}
