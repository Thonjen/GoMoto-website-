<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_id',
        'payment_mode_id',
        'type',
        'amount',
        'reference_number',
        'receipt_image',
        'paid_at',
    ];

    public function paymentMode()
    {
        return $this->belongsTo(PaymentMode::class);
    }
    
    public function booking()
    {
        return $this->belongsTo(\App\Models\Booking::class);
    }
}