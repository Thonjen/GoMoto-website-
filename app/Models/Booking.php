<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
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
    ];

    protected $casts = [
        'pickup_datetime' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

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
     */
    public function getCalculatedEndDatetimeAttribute()
    {
        if (!$this->pickup_datetime || !$this->pricingTier) {
            return null;
        }

        $tier = $this->pricingTier;
        $pickup = $this->pickup_datetime;

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
}
