<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KycVerificationLog extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'admin_id',
        'action',
        'old_status',
        'new_status',
        'reason',
        'documents_checked',
        'ip_address',
        'user_agent'
    ];
    
    protected $casts = [
        'documents_checked' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
    
    public function getActionLabelAttribute()
    {
        return match($this->action) {
            'submitted' => 'Documents Submitted',
            'approved' => 'KYC Approved',
            'rejected' => 'KYC Rejected',
            'resubmitted' => 'Documents Resubmitted',
            default => ucfirst($this->action)
        };
    }
    
    public function getStatusBadgeClassAttribute()
    {
        return match($this->action) {
            'submitted', 'resubmitted' => 'bg-blue-100 text-blue-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }
}
