<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarSeeder extends Seeder
{
    public function run()
    {
        $cars = [
            ['make' => 'Toyota', 'model' => 'Vios', 'year' => '2020', 'transmission' => 'Automatic', 'fuel_type' => 'Gasoline'],
            ['make' => 'Toyota', 'model' => 'Hilux', 'year' => '2022', 'transmission' => 'Automatic', 'fuel_type' => 'Diesel (Used)'],
            ['make' => 'Mitsubishi', 'model' => 'Xpander', 'year' => '2021', 'transmission' => 'Automatic', 'fuel_type' => 'Gasoline'],
            ['make' => 'Mitsubishi', 'model' => 'Triton', 'year' => '2021', 'transmission' => 'Automatic', 'fuel_type' => 'Diesel'],
            ['make' => 'BYD', 'model' => 'Sealion 6 DM‑i (PHEV)', 'year' => '2024', 'transmission' => 'Automatic', 'fuel_type' => 'Plug-in Hybrid'],
            ['make' => 'Toyota', 'model' => 'Wigo', 'year' => '2020', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'Toyota', 'model' => 'Raize', 'year' => '2022', 'transmission' => 'Automatic', 'fuel_type' => 'Gasoline'],
            ['make' => 'Toyota', 'model' => 'Avanza', 'year' => '2021', 'transmission' => 'Automatic', 'fuel_type' => 'Gasoline'],
            ['make' => 'Toyota', 'model' => 'Innova', 'year' => '2020', 'transmission' => 'Automatic', 'fuel_type' => 'Gasoline'],
            ['make' => 'Toyota', 'model' => 'Hiace', 'year' => '2020', 'transmission' => 'Manual', 'fuel_type' => 'Diesel'],
            ['make' => 'Toyota', 'model' => 'Innova Zenix (Hybrid)', 'year' => '2023', 'transmission' => 'Automatic', 'fuel_type' => 'Hybrid'],
            ['make' => 'Nissan', 'model' => 'Almera', 'year' => '2021', 'transmission' => 'Automatic', 'fuel_type' => 'Gasoline (Turbo)'],
            ['make' => 'Toyota', 'model' => 'Corolla Cross (Hybrid)', 'year' => '2021', 'transmission' => 'Automatic', 'fuel_type' => 'Gasoline-Hybrid'],
            ['make' => 'Hyundai', 'model' => 'Stargazer', 'year' => '2022', 'transmission' => 'Automatic', 'fuel_type' => 'Gasoline'],
            ['make' => 'BYD', 'model' => 'Seal 5 DM‑i (PHEV)', 'year' => '2024', 'transmission' => 'Automatic', 'fuel_type' => 'Plug‑in Hybrid'],
            ['make' => 'BYD', 'model' => 'Seagull (EV)', 'year' => '2024', 'transmission' => 'Automatic', 'fuel_type' => 'Electric'],
            ['make' => 'Jetour', 'model' => 'Ice Cream EV', 'year' => '2024', 'transmission' => 'Automatic', 'fuel_type' => 'Electric'],
            ['make' => 'VinFast', 'model' => 'VF 3 (EV)', 'year' => '2024', 'transmission' => 'Automatic', 'fuel_type' => 'Electric'],
            ['make' => 'Kia', 'model' => 'Sonet', 'year' => '2023', 'transmission' => 'Automatic', 'fuel_type' => 'Gasoline'],
            ['make' => 'MG', 'model' => '3 Hybrid+, MG5', 'year' => '2023', 'transmission' => 'Automatic', 'fuel_type' => 'Hybrid'],
            ['make' => 'Suzuki', 'model' => 'S-Presso', 'year' => '2021', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'Hyundai', 'model' => 'Eon', 'year' => '2017', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'Ford', 'model' => 'Everest', 'year' => '2020', 'transmission' => 'Automatic', 'fuel_type' => 'Diesel'],
            ['make' => 'Nissan', 'model' => 'Navara', 'year' => '2020', 'transmission' => 'Automatic', 'fuel_type' => 'Diesel'],
            ['make' => 'Ford', 'model' => 'Ranger', 'year' => '2020', 'transmission' => 'Automatic', 'fuel_type' => 'Diesel'],
            ['make' => 'Mitsubishi', 'model' => 'Montero Sport', 'year' => '2020', 'transmission' => 'Automatic', 'fuel_type' => 'Diesel'],
            ['make' => 'BYD', 'model' => 'Atto 3 (EV)', 'year' => '2023', 'transmission' => 'Automatic', 'fuel_type' => 'Electric'],
            ['make' => 'Toyota', 'model' => 'Yaris Cross (Hybrid)', 'year' => '2023', 'transmission' => 'Automatic', 'fuel_type' => 'Hybrid'],
            ['make' => 'Mazda', 'model' => 'Mazda 2 (Hatchback)', 'year' => '2020', 'transmission' => 'Automatic', 'fuel_type' => 'Gasoline'],
            ['make' => 'Kia', 'model' => 'Seltos SX Turbo', 'year' => '2023', 'transmission' => 'Automatic', 'fuel_type' => 'Gasoline (Turbo)'],
            ['make' => 'Suzuki', 'model' => 'Jimny 5-Door', 'year' => '2023', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'Toyota', 'model' => 'RAV4 (Hybrid)', 'year' => '2022', 'transmission' => 'Automatic', 'fuel_type' => 'Hybrid'],
            ['make' => 'Toyota', 'model' => 'Alphard (Hybrid)', 'year' => '2023', 'transmission' => 'Automatic', 'fuel_type' => 'Hybrid'],
            ['make' => 'Toyota', 'model' => 'Yaris Cross V', 'year' => '2023', 'transmission' => 'Automatic', 'fuel_type' => 'Gasoline'],
            ['make' => 'Honda', 'model' => 'Civic e:HEV (Hybrid)', 'year' => '2023', 'transmission' => 'Automatic', 'fuel_type' => 'Hybrid'],
            ['make' => 'Chery', 'model' => 'Tiggo 8 / Tiggo 8 Pro (PHEV)', 'year' => '2023', 'transmission' => 'Automatic', 'fuel_type' => 'Gasoline / PHEV'],
            ['make' => 'Changan', 'model' => 'Nevo Q05 (PHEV SUV)', 'year' => '2024', 'transmission' => 'Automatic', 'fuel_type' => 'PHEV'],
            ['make' => 'Chery', 'model' => 'Tiggo Grand Tour (MPV)', 'year' => '2024', 'transmission' => 'Automatic', 'fuel_type' => 'Gasoline'],
            ['make' => 'BYD', 'model' => 'eMAX (PHEV MPV)', 'year' => '2024', 'transmission' => 'Automatic', 'fuel_type' => 'PHEV'],
        ];

        DB::table('cars')->insert($cars);
    }
}
