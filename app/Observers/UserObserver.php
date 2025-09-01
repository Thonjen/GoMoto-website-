<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Role;

class UserObserver
{
    /**
     * Handle the User "creating" event.
     */
    public function creating(User $user): void
    {
        // If user is being created with admin role, set appropriate permissions
        if ($user->role_id) {
            $role = Role::find($user->role_id);
            if ($role && $role->name === 'admin') {
                $this->setAdminPermissions($user);
            }
        }
    }

    /**
     * Handle the User "updating" event.
     */
    public function updating(User $user): void
    {
        // If role is being changed to admin, set appropriate permissions
        if ($user->isDirty('role_id') && $user->role_id) {
            $role = Role::find($user->role_id);
            if ($role && $role->name === 'admin') {
                $this->setAdminPermissions($user);
            }
        }
    }

    /**
     * Set admin permissions for a user
     */
    private function setAdminPermissions(User $user): void
    {
        $user->can_book = true;
        $user->can_list_vehicles = true;
        $user->kyc_status = 'approved';
        $user->kyc_verified_at = now();
        $user->status = 'active';
        
        // Set email as verified if not already set
        if (!$user->email_verified_at) {
            $user->email_verified_at = now();
        }
    }
}
