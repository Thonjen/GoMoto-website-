<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'make_id',
        'name',
        'model_id',
    ];

    protected $casts = [
        'make_id' => 'integer',
        'model_id' => 'integer',
    ];

    public function make()
    {
        return $this->belongsTo(VehicleMake::class, 'make_id');
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'model_id');
    }

    // Scope for filtering by make
    public function scopeForMake($query, $makeId)
    {
        return $query->where('make_id', $makeId);
    }

    // Get models ordered by name
    public function scopeOrderedByName($query)
    {
        return $query->orderBy('name');
    }
}
