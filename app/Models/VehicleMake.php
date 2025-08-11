<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleMake extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'vehicle_type',
        'make_id',
    ];

    protected $casts = [
        'make_id' => 'integer',
    ];

    public function models()
    {
        return $this->hasMany(VehicleModel::class, 'make_id');
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'make_id');
    }

    // Scope for filtering by vehicle type (car or motorcycle)
    public function scopeForVehicleType($query, $vehicleType)
    {
        return $query->where('vehicle_type', $vehicleType);
    }

    // Get makes ordered by name
    public function scopeOrderedByName($query)
    {
        return $query->orderBy('name');
    }
}
