<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Brand;
use App\Models\VehicleType;
use App\Models\FuelType;
use App\Models\PaymentMode;


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
        
        Brand::updateOrCreate(['id' => 1], ['name' => 'Toyota']);
        Brand::updateOrCreate(['id' => 2], ['name' => 'Honda']);
        
        VehicleType::updateOrCreate(['id' => 1], ['category' => 'car', 'sub_type' => 'SUV']);
        
        FuelType::updateOrCreate(['id' => 1], ['name' => 'Gasoline']);
        FuelType::updateOrCreate(['id' => 2], ['name' => 'Diesel']);
        
        // Payment modes
        PaymentMode::updateOrCreate(['id' => 1], ['name' => 'GCash QR']);
        PaymentMode::updateOrCreate(['id' => 2], ['name' => 'Cash on Delivery']);
    }

}
