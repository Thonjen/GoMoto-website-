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
        'brand_id',
        'type_id',
        'fuel_type_id',
        'license_plate',
        'year',
        'color',
        'is_available',
        'description',
        'main_photo_url',
        'lat',
        'lng',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];


    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function type()
    {
        return $this->belongsTo(VehicleType::class, 'type_id');
    }

    public function fuelType()
    {
        return $this->belongsTo(FuelType::class, 'fuel_type_id');
    }

    public function photos()
    {
        return $this->hasMany(VehiclePhoto::class);
    }
}
