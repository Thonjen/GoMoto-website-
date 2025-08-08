<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

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
        'role_id',
        'password',
        'gcash_qr',
        'accepted_payment_methods',
        'accepts_cod',
        'accepts_gcash',
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
        ];
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
}
