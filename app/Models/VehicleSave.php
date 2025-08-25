<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleSave extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vehicle_id',
        'list_name',
        'notes',
        'saved_at',
    ];

    protected $casts = [
        'saved_at' => 'datetime',
    ];

    /**
     * Get the user who saved the vehicle
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the saved vehicle
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Scope for a specific user's saves
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for a specific wishlist
     */
    public function scopeInList($query, $listName)
    {
        return $query->where('list_name', $listName);
    }

    /**
     * Scope for recent saves
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('saved_at', '>=', now()->subDays($days));
    }

    /**
     * Get all unique list names for a user
     */
    public static function getUserWishlists($userId)
    {
        return static::where('user_id', $userId)
            ->distinct('list_name')
            ->pluck('list_name')
            ->values();
    }

    /**
     * Check if a vehicle is saved by a user
     */
    public static function isSaved($userId, $vehicleId, $listName = 'My Saved Vehicles')
    {
        return static::where('user_id', $userId)
            ->where('vehicle_id', $vehicleId)
            ->where('list_name', $listName)
            ->exists();
    }

    /**
     * Get save count for a vehicle
     */
    public static function getSaveCount($vehicleId)
    {
        return static::where('vehicle_id', $vehicleId)->count();
    }

    /**
     * Get most saved vehicles
     */
    public static function getMostSaved($limit = 10)
    {
        return static::select('vehicle_id')
            ->groupBy('vehicle_id')
            ->orderByRaw('COUNT(*) DESC')
            ->limit($limit)
            ->with('vehicle')
            ->get()
            ->pluck('vehicle');
    }
}
