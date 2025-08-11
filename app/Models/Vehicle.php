<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'owner_id',
        'make_id',
        'model_id',
        'type_id',
        'fuel_type_id',
        'transmission_id',
        'license_plate',
        'year',
        'color',
        'engine_size',
        'horsepower',
        'doors',
        'seats',
        'fuel_efficiency',
        'is_available',
        'description',
        'main_photo_url',
        'lat',
        'lng',
        'location_name',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    protected $appends = ['pricing_tiers'];


    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }


    public function type()
    {
        return $this->belongsTo(VehicleType::class, 'type_id');
    }

    public function fuelType()
    {
        return $this->belongsTo(FuelType::class, 'fuel_type_id');
    }

    public function make()
    {
        return $this->belongsTo(VehicleMake::class, 'make_id');
    }

    public function model()
    {
        return $this->belongsTo(VehicleModel::class, 'model_id');
    }

    public function transmission()
    {
        return $this->belongsTo(Transmission::class, 'transmission_id');
    }

    public function photos()
    {
        return $this->hasMany(VehiclePhoto::class);
    }

    public function pricingTiers()
    {
        return $this->belongsToMany(VehiclePricingTier::class, 'vehicle_vehicle_pricing_tier', 'vehicle_id', 'vehicle_pricing_tier_id');
    }

    // Add this accessor to always expose pricing_tiers as an attribute
    public function getPricingTiersAttribute()
    {
        return $this->pricingTiers()->get();
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get overcharge settings for this vehicle (inherited from owner)
     */
    public function getOverchargeSettings()
    {
        return [
            'late_return_rate' => $this->owner->late_return_rate ?? 100.00,
            'out_of_city_base' => $this->owner->out_of_city_base ?? 500.00,
            'out_of_city_rate' => $this->owner->out_of_city_rate ?? 50.00,
            'grace_period_minutes' => $this->owner->grace_period_minutes ?? 30,
        ];
    }

    /**
     * Get overcharge statistics for this vehicle
     */
    public function getOverchargeStats()
    {
        $bookings = $this->bookings()->with(['overcharges.overchargeType'])->get();
        
        return [
            'total_overcharges' => $bookings->sum('total_overcharges'),
            'late_returns' => $bookings->filter(function($booking) {
                return $booking->overcharges->where('overcharge_type.name', 'late_return')->count() > 0;
            })->count(),
            'out_of_city_returns' => $bookings->filter(function($booking) {
                return $booking->overcharges->where('overcharge_type.name', 'out_of_city')->count() > 0;
            })->count(),
            'average_overcharge' => $bookings->where('total_overcharges', '>', 0)->avg('total_overcharges') ?? 0,
        ];
    }
}
