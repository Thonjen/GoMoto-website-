<?php

namespace Database\Seeders;

use App\Models\Transmission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransmissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $transmissions = [
            [
                'name' => 'Manual',
                'description' => 'Manual transmission (stick shift)'
            ],
            [
                'name' => 'Automatic',
                'description' => 'Traditional automatic transmission'
            ],
            [
                'name' => 'CVT',
                'description' => 'Continuously Variable Transmission'
            ],
            [
                'name' => 'Semi-Automatic',
                'description' => 'Semi-automatic transmission (clutchless manual)'
            ],
            [
                'name' => 'Dual-Clutch',
                'description' => 'Dual-clutch transmission (DCT)'
            ],
        ];

        foreach ($transmissions as $transmission) {
            Transmission::firstOrCreate(
                ['name' => $transmission['name']],
                $transmission
            );
        }
    }
}
