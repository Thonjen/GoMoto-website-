<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;

class GrantAdminRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:make-admin {email : The email of the user to promote to admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Grant admin role to a user by email address';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        // Find the user
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email '{$email}' not found.");
            return 1;
        }
        
        // Find the admin role
        $adminRole = Role::where('name', 'admin')->first();
        
        if (!$adminRole) {
            $this->error("Admin role not found. Please run the RoleSeeder first.");
            return 1;
        }
        
        // Check if user is already admin
        if ($user->role_id === $adminRole->id) {
            $this->info("User '{$user->name}' is already an admin.");
            return 0;
        }
        
        // Update user role and permissions
        $user->update([
            'role_id' => $adminRole->id,
            'can_book' => true,
            'can_list_vehicles' => true,
            'kyc_status' => 'approved',
            'kyc_verified_at' => now(),
            'email_verified_at' => now(),
            'status' => 'active'
        ]);
        
        $this->info("User '{$user->name}' ({$email}) has been promoted to admin successfully!");
        $this->info("Admin permissions granted:");
        $this->line("- Can book vehicles: Yes");
        $this->line("- Can list vehicles: Yes");
        $this->line("- KYC status: Approved");
        $this->line("- Email verified: Yes");
        $this->line("- Account status: Active");
        
        return 0;
    }
}
