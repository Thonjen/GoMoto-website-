<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Overcharge extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'overcharge_type_id',
        'amount',
        'details',
        'units',
        'rate_applied',
        'is_paid',
        'occurred_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'rate_applied' => 'decimal:2',
        'is_paid' => 'boolean',
        'occurred_at' => 'datetime'
    ];

    /**
     * Get the booking that this overcharge belongs to
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the overcharge type
     */
    public function overchargeType()
    {
        return $this->belongsTo(OverchargeType::class);
    }

    /**
     * Scope for unpaid overcharges
     */
    public function scopeUnpaid($query)
    {
        return $query->where('is_paid', false);
    }

    /**
     * Scope for paid overcharges
     */
    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }

    /**
     * Mark overcharge as paid
     */
    public function markAsPaid()
    {
        $this->update(['is_paid' => true]);
        
        // Update booking's total overcharges
        $this->booking->updateOverchargeStatus();
    }
}
