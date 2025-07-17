<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        
        \App\Models\Role::updateOrCreate(['id' => 1], ['name' => 'admin']);
        \App\Models\Role::updateOrCreate(['id' => 2], ['name' => 'renter']);
        \App\Models\Role::updateOrCreate(['id' => 3], ['name' => 'owner']);
    }
}
