<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminPermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'owner']);
        Role::create(['name' => 'customer']);
    }

    public function test_admin_user_can_make_bookings_without_kyc()
    {
        $adminRole = Role::where('name', 'admin')->first();
        
        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
            'kyc_status' => 'pending', // Not approved
            'can_book' => false, // Not explicitly allowed
            'drivers_license_front' => null, // No license
            'drivers_license_back' => null,
        ]);

        $this->assertTrue($admin->canMakeBookings());
    }

    public function test_admin_user_can_list_vehicles_without_kyc()
    {
        $adminRole = Role::where('name', 'admin')->first();
        
        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
            'kyc_status' => 'pending', // Not approved
            'can_list_vehicles' => false, // Not explicitly allowed
        ]);

        $this->assertTrue($admin->canListVehicles());
    }

    public function test_non_admin_user_needs_kyc_for_bookings()
    {
        $customerRole = Role::where('name', 'customer')->first();
        
        $customer = User::factory()->create([
            'role_id' => $customerRole->id,
            'kyc_status' => 'pending',
            'can_book' => false,
        ]);

        $this->assertFalse($customer->canMakeBookings());
    }

    public function test_admin_user_has_verified_email_automatically()
    {
        $adminRole = Role::where('name', 'admin')->first();
        
        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
            'email_verified_at' => null, // Not verified
        ]);

        $this->assertTrue($admin->hasVerifiedEmail());
    }

    public function test_admin_role_helper_methods()
    {
        $adminRole = Role::where('name', 'admin')->first();
        $customerRole = Role::where('name', 'customer')->first();
        
        $admin = User::factory()->create(['role_id' => $adminRole->id]);
        $customer = User::factory()->create(['role_id' => $customerRole->id]);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isCustomer());
        
        $this->assertFalse($customer->isAdmin());
        $this->assertTrue($customer->isCustomer());
    }

    public function test_admin_does_not_require_kyc_verification()
    {
        $adminRole = Role::where('name', 'admin')->first();
        $customerRole = Role::where('name', 'customer')->first();
        
        $admin = User::factory()->create(['role_id' => $adminRole->id]);
        $customer = User::factory()->create(['role_id' => $customerRole->id]);

        $this->assertFalse($admin->requiresKycVerification());
        $this->assertTrue($customer->requiresKycVerification());
    }
}
