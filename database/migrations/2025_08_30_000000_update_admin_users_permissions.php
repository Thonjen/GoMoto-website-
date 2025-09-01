<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Find admin role
        $adminRole = Role::where('name', 'admin')->first();
        
        if ($adminRole) {
            // Update all admin users to have booking and listing permissions
            // Also set their KYC status to approved and email verified
            User::where('role_id', $adminRole->id)
                ->update([
                    'can_book' => true,
                    'can_list_vehicles' => true,
                    'kyc_status' => 'approved',
                    'kyc_verified_at' => now(),
                    'email_verified_at' => now(),
                    'status' => 'active'
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Find admin role
        $adminRole = Role::where('name', 'admin')->first();
        
        if ($adminRole) {
            // Revert admin users permissions (optional - you may not want to do this)
            User::where('role_id', $adminRole->id)
                ->update([
                    'can_book' => false,
                    'can_list_vehicles' => false,
                    'kyc_status' => 'pending',
                    'kyc_verified_at' => null
                ]);
        }
    }
};
