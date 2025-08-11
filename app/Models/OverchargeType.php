<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OverchargeType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'rate_per_hour',
        'rate_per_km',
        'base_rate',
        'is_active'
    ];

    protected $casts = [
        'rate_per_hour' => 'decimal:2',
        'rate_per_km' => 'decimal:2',
        'base_rate' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    /**
     * Get all overcharges of this type
     */
    public function overcharges()
    {
        return $this->hasMany(Overcharge::class);
    }

    /**
     * Scope for active overcharge types
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get overcharge type by name
     */
    public static function getByName($name)
    {
        return self::where('name', $name)->where('is_active', true)->first();
    }
}
