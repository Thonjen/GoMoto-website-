<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class VehicleAvailabilityBlock extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'blocked_date',
        'block_type',
        'reason',
        'notes',
        'is_recurring',
        'recurring_type',
        'recurring_pattern',
        'recurring_end_date',
    ];

    protected $casts = [
        'blocked_date' => 'date',
        'recurring_end_date' => 'date',
        'is_recurring' => 'boolean',
        'recurring_pattern' => 'array',
    ];

    /**
     * Relationships
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Scope to get blocks for a specific date range
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('blocked_date', [$startDate, $endDate]);
    }

    /**
     * Scope to get blocks for a specific vehicle
     */
    public function scopeForVehicle($query, $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    /**
     * Scope to get active blocks (including future recurring dates)
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->where('blocked_date', '>=', now()->toDateString())
              ->orWhere(function ($subQ) {
                  $subQ->where('is_recurring', true)
                       ->where(function ($dateQ) {
                           $dateQ->whereNull('recurring_end_date')
                                 ->orWhere('recurring_end_date', '>=', now()->toDateString());
                       });
              });
        });
    }

    /**
     * Check if a specific date is blocked for a vehicle
     */
    public static function isDateBlocked($vehicleId, $date)
    {
        $date = Carbon::parse($date)->toDateString();
        
        // Check direct blocks
        $directBlock = self::where('vehicle_id', $vehicleId)
                          ->where('blocked_date', $date)
                          ->exists();
        
        if ($directBlock) {
            return true;
        }
        
        // Check recurring blocks
        $recurringBlocks = self::where('vehicle_id', $vehicleId)
                              ->where('is_recurring', true)
                              ->where(function ($q) use ($date) {
                                  $q->whereNull('recurring_end_date')
                                    ->orWhere('recurring_end_date', '>=', $date);
                              })
                              ->get();
        
        foreach ($recurringBlocks as $block) {
            if (self::isDateInRecurringPattern($block, $date)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if a date range has any blocked dates for a vehicle
     */
    public static function hasBlockedDatesInRange($vehicleId, $startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        $current = $start->copy();
        while ($current->lte($end)) {
            if (self::isDateBlocked($vehicleId, $current->toDateString())) {
                return true;
            }
            $current->addDay();
        }
        
        return false;
    }

    /**
     * Get all blocked dates for a vehicle in a date range
     */
    public static function getBlockedDatesInRange($vehicleId, $startDate, $endDate)
    {
        $blockedDates = [];
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        $current = $start->copy();
        while ($current->lte($end)) {
            if (self::isDateBlocked($vehicleId, $current->toDateString())) {
                $blockedDates[] = $current->toDateString();
            }
            $current->addDay();
        }
        
        return $blockedDates;
    }

    /**
     * Check if a date matches a recurring pattern
     */
    private static function isDateInRecurringPattern($block, $date)
    {
        $checkDate = Carbon::parse($date);
        $originalDate = Carbon::parse($block->blocked_date);
        
        switch ($block->recurring_type) {
            case 'weekly':
                // Same day of week
                return $checkDate->dayOfWeek === $originalDate->dayOfWeek && 
                       $checkDate->gte($originalDate);
                
            case 'monthly':
                // Same day of month
                return $checkDate->day === $originalDate->day && 
                       $checkDate->gte($originalDate);
                
            case 'yearly':
                // Same month and day
                return $checkDate->month === $originalDate->month && 
                       $checkDate->day === $originalDate->day && 
                       $checkDate->gte($originalDate);
                
            default:
                return false;
        }
    }

    /**
     * Generate recurring dates for a specific period
     */
    public function generateRecurringDates($startDate, $endDate)
    {
        if (!$this->is_recurring) {
            return [];
        }
        
        $dates = [];
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $originalDate = Carbon::parse($this->blocked_date);
        
        $current = $start->copy();
        while ($current->lte($end)) {
            if (self::isDateInRecurringPattern($this, $current->toDateString())) {
                $dates[] = $current->toDateString();
            }
            $current->addDay();
        }
        
        return $dates;
    }

    /**
     * Get block type options
     */
    public static function getBlockTypes()
    {
        return [
            'maintenance' => 'Maintenance',
            'personal_use' => 'Personal Use',
            'repairs' => 'Repairs',
            'seasonal' => 'Seasonal',
            'other' => 'Other',
        ];
    }

    /**
     * Get recurring type options
     */
    public static function getRecurringTypes()
    {
        return [
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'yearly' => 'Yearly',
        ];
    }
}
