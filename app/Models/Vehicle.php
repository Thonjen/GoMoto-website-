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
        'status',
        'allow_extensions',
        'max_extension_hours',
        'require_approval_for_extensions',
        'description',
        'main_photo_url',
        'lat',
        'lng',
        'location_name',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'allow_extensions' => 'boolean',
        'require_approval_for_extensions' => 'boolean',
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

    public function vehicleType()
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

    public function brand()
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

    public function ratings()
    {
        return $this->hasMany(VehicleRating::class);
    }

    /**
     * Get the availability blocks for this vehicle
     */
    public function availabilityBlocks()
    {
        return $this->hasMany(VehicleAvailabilityBlock::class);
    }

    /**
     * Get the saves for this vehicle
     */
    public function saves()
    {
        return $this->hasMany(VehicleSave::class);
    }

    /**
     * Get users who saved this vehicle
     */
    public function savedByUsers()
    {
        return $this->hasManyThrough(
            User::class,
            VehicleSave::class,
            'vehicle_id',
            'id',
            'id',
            'user_id'
        );
    }

    /**
     * Get save count for this vehicle
     */
    public function getSaveCountAttribute()
    {
        return $this->saves()->count();
    }

    /**
     * Check if vehicle is saved by a specific user
     */
    public function isSavedBy($userId, $listName = 'My Saved Vehicles')
    {
        return $this->saves()
            ->where('user_id', $userId)
            ->where('list_name', $listName)
            ->exists();
    }

    /**
     * Get average rating for this vehicle
     */
    public function getAverageRatingAttribute()
    {
        $rating = $this->ratings()->avg('rating');
        return $rating ? (float) $rating : 0.0;
    }

    /**
     * Get total number of ratings
     */
    public function getTotalRatingsAttribute()
    {
        return $this->ratings()->count();
    }

    /**
     * Get rating distribution (1-5 stars)
     */
    public function getRatingDistributionAttribute()
    {
        $distribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $distribution[$i] = $this->ratings()->where('rating', $i)->count();
        }
        return $distribution;
    }

    /**
     * Get recent ratings (last 10)
     */
    public function getRecentRatingsAttribute()
    {
        return $this->ratings()
                    ->with(['user'])
                    ->orderBy('rated_at', 'desc')
                    ->limit(10)
                    ->get();
    }

    /**
     * Check if vehicle has good ratings (4+ stars average with at least 3 ratings)
     */
    public function hasGoodRatingsAttribute()
    {
        return $this->total_ratings >= 3 && $this->average_rating >= 4.0;
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
