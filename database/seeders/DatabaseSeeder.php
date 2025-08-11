<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(VehicleTypeSeeder::class);
        $this->call(TransmissionSeeder::class);

        \App\Models\User::updateOrCreate([
            'email' => 'admin@example.com',
        ], [
            'first_name' => 'Admin',
            'last_name' => 'User',
            'phone' => '09170000001',
            'role_id' => 1,
            'password' => bcrypt('password'),
        ]);

        \App\Models\User::updateOrCreate([
            'email' => 'renter@example.com',
        ], [
            'first_name' => 'Renter',
            'last_name' => 'User',
            'phone' => '09170000002',
            'role_id' => 2,
            'password' => bcrypt('password'),
        ]);

        \App\Models\User::updateOrCreate([
            'email' => 'owner@example.com',
        ], [
            'first_name' => 'Owner',
            'last_name' => 'User',
            'phone' => '09170000003',
            'role_id' => 3,
            'password' => bcrypt('password'),
            'accepts_cod' => true,
            'accepts_gcash' => false,
        ]);
    }
}
