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
        'start_datetime',
        'end_datetime',
        'status',
        'pickup_place_id',
        'dropoff_place_id',
        'total_amount',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
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

    // Optionally, if you want to store selected tier:
    // public function pricingTier()
    // {
    //     return $this->belongsTo(\App\Models\VehiclePricingTier::class, 'pricing_tier_id');
    // }

    public function getDurationInDaysAttribute()
    {
        return $this->start_datetime->diffInDays($this->end_datetime) + 1;
    }
}
