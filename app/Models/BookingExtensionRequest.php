<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingExtensionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'requested_hours',
        'reason',
        'calculated_cost',
        'status',
        'owner_notes',
        'approved_at',
        'rejected_at',
        'approved_by',
    ];

    protected $casts = [
        'calculated_cost' => 'decimal:2',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    /**
     * Get the booking this extension request belongs to
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the user who approved/rejected the request
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope for pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved requests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for rejected requests
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Approve the extension request
     */
    public function approve(User $approver, string $notes = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $approver->id,
            'owner_notes' => $notes,
        ]);

        // Apply the extension to the booking
        $this->booking->applyExtension($this->requested_hours, $this->calculated_cost);
    }

    /**
     * Reject the extension request
     */
    public function reject(User $rejector, string $notes = null)
    {
        $this->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'approved_by' => $rejector->id,
            'owner_notes' => $notes,
        ]);
    }
}
