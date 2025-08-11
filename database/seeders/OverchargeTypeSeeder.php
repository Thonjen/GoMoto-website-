<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OverchargeType;

class OverchargeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $overchargeTypes = [
            [
                'name' => 'late_return',
                'description' => 'Overcharge for returning vehicle late',
                'rate_per_hour' => 100.00, // PHP 100 per hour late
                'rate_per_km' => null,
                'base_rate' => null,
                'is_active' => true
            ],
            [
                'name' => 'out_of_city',
                'description' => 'Overcharge for returning vehicle outside city limits',
                'rate_per_hour' => null,
                'rate_per_km' => 50.00, // PHP 50 per km outside city
                'base_rate' => 500.00, // PHP 500 base charge for out of city
                'is_active' => true
            ]
        ];

        foreach ($overchargeTypes as $type) {
            OverchargeType::updateOrCreate(
                ['name' => $type['name']],
                $type
            );
        }
    }
}
