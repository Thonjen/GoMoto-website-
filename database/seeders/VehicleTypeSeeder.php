<?php

namespace Database\Seeders;

use App\Models\VehicleType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehicleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicleTypes = [
            [
                'category' => 'car',
                'sub_type' => 'Sedan',
            ],
            [
                'category' => 'car',
                'sub_type' => 'SUV',
            ],
            [
                'category' => 'car',
                'sub_type' => 'Hatchback',
            ],
            [
                'category' => 'car',
                'sub_type' => 'Pickup',
            ],
            [
                'category' => 'car',
                'sub_type' => 'Coupe',
            ],
            [
                'category' => 'car',
                'sub_type' => 'Convertible',
            ],
            [
                'category' => 'car',
                'sub_type' => 'Van',
            ],
            [
                'category' => 'motorcycle',
                'sub_type' => 'Sport',
            ],
            [
                'category' => 'motorcycle',
                'sub_type' => 'Cruiser',
            ],
            [
                'category' => 'motorcycle',
                'sub_type' => 'Touring',
            ],
            [
                'category' => 'motorcycle',
                'sub_type' => 'Standard',
            ],
            [
                'category' => 'motorcycle',
                'sub_type' => 'Scooter',
            ],
            [
                'category' => 'motorcycle',
                'sub_type' => 'Adventure',
            ],
            [
                'category' => 'motorcycle',
                'sub_type' => 'Dirt Bike',
            ],
        ];

        foreach ($vehicleTypes as $vehicleType) {
            VehicleType::firstOrCreate(
                [
                    'category' => $vehicleType['category'],
                    'sub_type' => $vehicleType['sub_type']
                ],
                $vehicleType
            );
        }
    }
}
