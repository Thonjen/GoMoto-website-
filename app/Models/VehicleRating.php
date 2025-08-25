<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class VehicleRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vehicle_id',
        'booking_id',
        'rating',
        'comment',
        'would_recommend',
        'rating_categories',
        'is_featured',
        'rated_at',
    ];

    protected $casts = [
        'rating_categories' => 'array',
        'would_recommend' => 'boolean',
        'is_featured' => 'boolean',
        'rated_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Scopes
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeHighRated($query, $threshold = 4)
    {
        return $query->where('rating', '>=', $threshold);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('rated_at', '>=', Carbon::now()->subDays($days));
    }

    /**
     * Accessors
     */
    public function getStarsDisplayAttribute()
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }

    public function getFormattedDateAttribute()
    {
        return $this->rated_at->format('M j, Y');
    }

    /**
     * Static methods for analytics
     */
    public static function getAverageRating($vehicleId)
    {
        return self::where('vehicle_id', $vehicleId)->avg('rating') ?? 0;
    }

    public static function getTotalRatings($vehicleId)
    {
        return self::where('vehicle_id', $vehicleId)->count();
    }

    public static function getRatingDistribution($vehicleId)
    {
        $distribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $distribution[$i] = self::where('vehicle_id', $vehicleId)
                                   ->where('rating', $i)
                                   ->count();
        }
        return $distribution;
    }
}
