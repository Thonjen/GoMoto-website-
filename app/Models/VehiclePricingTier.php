<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehiclePricingTier extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'owner_id',
        'duration_from',
        'duration_unit',
        'price',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function owner()
    {
        return $this->belongsTo(\App\Models\User::class, 'owner_id');
    }
}