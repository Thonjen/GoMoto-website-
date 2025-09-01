<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'profile_picture',
        'role_id',
        'password',
        'status',
        'gcash_qr',
        'accepted_payment_methods',
        'accepts_cod',
        'accepts_gcash',
        'late_return_rate',
        'out_of_city_base',
        'out_of_city_rate',
        'grace_period_minutes',
        'enable_overcharges',
        'overcharge_instructions',
        // New KYC fields
        'bio',
        'drivers_license_front',
        'drivers_license_back',
        'license_submitted_at',
        'kyc_status',
        'kyc_rejection_reason',
        'kyc_verified_at',
        'kyc_verified_by',
        'can_book',
        'can_list_vehicles',
        // Activity tracking fields
        'last_seen_at',
        'is_online',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'accepted_payment_methods' => 'array',
            'accepts_cod' => 'boolean',
            'accepts_gcash' => 'boolean',
            'late_return_rate' => 'decimal:2',
            'out_of_city_base' => 'decimal:2',
            'out_of_city_rate' => 'decimal:2',
            'enable_overcharges' => 'boolean',
            // New KYC casts
            'license_submitted_at' => 'datetime',
            'kyc_verified_at' => 'datetime',
            'can_book' => 'boolean',
            'can_list_vehicles' => 'boolean',
            // Activity tracking casts
            'last_seen_at' => 'datetime',
            'is_online' => 'boolean',
        ];
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['name', 'profile_picture_url', 'activity_status', 'activity_status_text'];

    /**
     * Get the user's full name.
     */
    public function getNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Get the user's profile picture URL with default fallback.
     */
    public function getProfilePictureUrlAttribute()
    {
        if ($this->profile_picture && Storage::disk('public')->exists($this->profile_picture)) {
            return Storage::url($this->profile_picture);
        }
        
        // Return default avatar based on user's initials
        return $this->getDefaultProfilePictureUrl();
    }

    /**
     * Get default profile picture URL using initials.
     */
    public function getDefaultProfilePictureUrl()
    {
        $initials = strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
        $backgroundColor = $this->getAvatarColor();
        
        // Use a service like UI Avatars to generate default avatars
        return "https://ui-avatars.com/api/?name={$initials}&background={$backgroundColor}&color=ffffff&size=200&font-size=0.6";
    }

    /**
     * Get a consistent color for the user's avatar based on their ID.
     */
    private function getAvatarColor()
    {
        $colors = [
            '3b82f6', // blue
            '10b981', // emerald
            'f59e0b', // amber
            'ef4444', // red
            '8b5cf6', // violet
            '06b6d4', // cyan
            'f97316', // orange
            'ec4899', // pink
            '84cc16', // lime
            '6366f1', // indigo
        ];
        
        return $colors[$this->id % count($colors)];
    }

    public function role()
{
    return $this->belongsTo(\App\Models\Role::class);
}

    public function vehicles()
    {
        return $this->hasMany(\App\Models\Vehicle::class, 'owner_id');
    }
    
    public function bookings()
    {
        return $this->hasMany(\App\Models\Booking::class);
    }
    
    public function pricingTiers()
    {
        return $this->hasMany(\App\Models\VehiclePricingTier::class, 'owner_id');
    }

    public function givenRatings()
    {
        return $this->hasMany(\App\Models\VehicleRating::class);
    }

    /**
     * Get the vehicles saved by this user
     */
    public function savedVehicles()
    {
        return $this->hasMany(\App\Models\VehicleSave::class);
    }

    /**
     * Get vehicles saved by this user through the saved vehicles relationship
     */
    public function vehiclesInWishlist($listName = 'My Saved Vehicles')
    {
        return $this->hasManyThrough(
            \App\Models\Vehicle::class,
            \App\Models\VehicleSave::class,
            'user_id',
            'id',
            'id',
            'vehicle_id'
        )->where('vehicle_saves.list_name', $listName);
    }

    /**
     * Check if user has saved a specific vehicle
     */
    public function hasSaved($vehicleId, $listName = 'My Saved Vehicles')
    {
        return $this->savedVehicles()
            ->where('vehicle_id', $vehicleId)
            ->where('list_name', $listName)
            ->exists();
    }
    
    /**
     * Get the available payment methods for this owner
     */
    public function getAvailablePaymentMethods()
    {
        $methods = [];
        
        if ($this->accepts_cod) {
            $methods[] = [
                'id' => 2, // Cash on Delivery payment mode ID
                'name' => 'Cash on Delivery',
                'type' => 'cod'
            ];
        }
        
        if ($this->accepts_gcash && $this->gcash_qr) {
            $methods[] = [
                'id' => 1, // GCash QR payment mode ID
                'name' => 'GCash QR',
                'type' => 'gcash'
            ];
        }
        
        return $methods;
    }
    
    /**
     * Check if owner can accept GCash payments (has QR code uploaded)
     */
    public function canAcceptGcash()
    {
        return $this->gcash_qr !== null;
    }
    
    /**
     * KYC verification relationships and methods
     */
    public function kycVerificationLogs()
    {
        return $this->hasMany(KycVerificationLog::class, 'user_id');
    }
    
    public function kycVerifiedBy()
    {
        return $this->belongsTo(User::class, 'kyc_verified_by');
    }
    
    public function isKycApproved()
    {
        return $this->kyc_status === 'approved';
    }
    
    public function isKycPending()
    {
        return $this->kyc_status === 'pending';
    }
    
    public function isKycUnderReview()
    {
        return $this->kyc_status === 'under_review';
    }
    
    public function isKycRejected()
    {
        return $this->kyc_status === 'rejected';
    }
    
    public function isActive()
    {
        return $this->status === 'active';
    }
    
    public function isBanned()
    {
        return $this->status === 'banned';
    }
    
    public function isSuspended()
    {
        return $this->status === 'suspended';
    }
    
    public function isAdmin()
    {
        return $this->role && $this->role->name === 'admin';
    }
    
    public function isOwner()
    {
        return $this->role && $this->role->name === 'owner';
    }
    
    public function isCustomer()
    {
        return $this->role && $this->role->name === 'customer';
    }
    
    public function hasDriversLicense()
    {
        return $this->drivers_license_front && $this->drivers_license_back;
    }
    
    /**
     * Activity tracking methods
     */
    public function updateLastSeen()
    {
        $this->update([
            'last_seen_at' => now(),
            'is_online' => true
        ]);
    }
    
    public function setOffline()
    {
        $this->update(['is_online' => false]);
    }
    
    public function isOnline()
    {
        return $this->is_online;
    }
    
    public function isActiveRecently($minutes = 5)
    {
        if (!$this->last_seen_at) {
            return false;
        }
        
        return $this->last_seen_at->diffInMinutes(now()) <= $minutes;
    }
    
    public function getActivityStatusAttribute()
    {
        if ($this->is_online) {
            return 'online';
        } elseif ($this->isActiveRecently()) {
            return 'recently_active';
        } elseif ($this->last_seen_at && $this->last_seen_at->diffInHours(now()) <= 24) {
            return 'active_today';
        } else {
            return 'offline';
        }
    }
    
    public function getActivityStatusTextAttribute()
    {
        return match($this->activity_status) {
            'online' => 'Online now',
            'recently_active' => 'Active recently',
            'active_today' => 'Active today',
            default => 'Offline'
        };
    }
    
    /**
     * Check if the user requires email verification
     * Admin users don't need email verification
     */
    public function hasVerifiedEmail()
    {
        // Admin users are automatically considered as having verified email
        if ($this->isAdmin()) {
            return true;
        }
        
        return !is_null($this->email_verified_at);
    }
    
    /**
     * Check if the user requires KYC verification
     * Admin users don't need KYC verification
     */
    public function requiresKycVerification()
    {
        return !$this->isAdmin();
    }
    
    public function canMakeBookings()
    {
        // Check if user is banned or suspended
        if (in_array($this->status, ['banned', 'suspended'])) {
            return false;
        }
        
        // Admin users can always make bookings (no KYC required)
        if ($this->isAdmin()) {
            return true;
        }
        
        return $this->can_book && $this->isKycApproved() && $this->hasDriversLicense();
    }
    
    public function canListVehicles()
    {
        // Check if user is banned or suspended
        if (in_array($this->status, ['banned', 'suspended'])) {
            return false;
        }
        
        // Admin users can always list vehicles (no KYC required)
        if ($this->isAdmin()) {
            return true;
        }
        
        return $this->can_list_vehicles && $this->isKycApproved();
    }
    
    public function getKycStatusBadgeClassAttribute()
    {
        return match($this->kyc_status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'under_review' => 'bg-blue-100 text-blue-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }
}
