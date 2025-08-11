<?php

namespace App\Console\Commands;

use App\Models\VehicleMake;
use App\Models\VehicleModel;
use App\Models\FuelType;
use App\Models\Transmission;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PopulatePhilippineVehicleData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vehicle-data:populate-ph {--fresh : Clear existing data first}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate vehicle makes and models from Philippine market data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Philippine vehicle data population...');

        if ($this->option('fresh')) {
            $this->info('Clearing existing data...');
            
            // Disable foreign key checks to allow truncation
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            VehicleModel::truncate();
            VehicleMake::truncate();
            FuelType::truncate();
            Transmission::truncate();
            
            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        // Create fuel types and transmissions
        $this->populateFuelTypes();
        $this->populateTransmissions();

        // Populate from car data
        $this->populateCarData();
        
        // Populate from motorcycle data
        $this->populateMotorcycleData();

        $this->info('Philippine vehicle data population completed!');
    }

    private function populateFuelTypes()
    {
        $this->info('Creating fuel types...');
        
        $fuelTypes = [
            'Gasoline',
            'Diesel', 
            'Electric',
            'Hybrid',
            'Plug-in Hybrid',
            'PHEV', // Plug-in Hybrid Electric Vehicle
            'Gasoline (Turbo)',
            'Gasoline-Hybrid',
            'Gasoline (2-stroke)'
        ];

        foreach ($fuelTypes as $name) {
            FuelType::firstOrCreate(['name' => $name]);
        }

        $this->info('Fuel types created.');
    }

    private function populateTransmissions()
    {
        $this->info('Creating transmission types...');
        
        $transmissions = [
            ['name' => 'Manual', 'description' => 'Manual transmission'],
            ['name' => 'Automatic', 'description' => 'Automatic transmission'],
            ['name' => 'CVT', 'description' => 'Continuously Variable Transmission'],
            ['name' => 'CVT (scooter)', 'description' => 'CVT for scooters'],
            ['name' => 'CVT (electric)', 'description' => 'CVT for electric vehicles'],
            ['name' => 'V-Matic (CVT)', 'description' => 'Honda V-Matic CVT'],
            ['name' => '4-speed rotary', 'description' => '4-speed rotary transmission'],
            ['name' => '5-speed manual', 'description' => '5-speed manual transmission'],
            ['name' => '4-speed manual', 'description' => '4-speed manual transmission'],
            ['name' => 'Electric (EV)', 'description' => 'Electric vehicle transmission'],
        ];

        foreach ($transmissions as $transmission) {
            Transmission::firstOrCreate(
                ['name' => $transmission['name']],
                $transmission
            );
        }

        $this->info('Transmission types created.');
    }

    private function populateCarData()
    {
        $this->info('Populating car data...');

        $cars = [
            ['make' => 'Toyota', 'model' => 'Vios', 'year' => '2020', 'transmission' => 'Automatic', 'fuel_type' => 'Gasoline'],
            ['make' => 'Toyota', 'model' => 'Hilux', 'year' => '2022', 'transmission' => 'Automatic', 'fuel_type' => 'Diesel'],
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
            ['make' => 'MG', 'model' => '3 Hybrid+', 'year' => '2023', 'transmission' => 'Automatic', 'fuel_type' => 'Hybrid'],
            ['make' => 'MG', 'model' => 'MG5', 'year' => '2023', 'transmission' => 'Automatic', 'fuel_type' => 'Hybrid'],
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
            ['make' => 'Chery', 'model' => 'Tiggo 8', 'year' => '2023', 'transmission' => 'Automatic', 'fuel_type' => 'Gasoline'],
            ['make' => 'Chery', 'model' => 'Tiggo 8 Pro (PHEV)', 'year' => '2023', 'transmission' => 'Automatic', 'fuel_type' => 'PHEV'],
            ['make' => 'Changan', 'model' => 'Nevo Q05 (PHEV SUV)', 'year' => '2024', 'transmission' => 'Automatic', 'fuel_type' => 'PHEV'],
            ['make' => 'Chery', 'model' => 'Tiggo Grand Tour (MPV)', 'year' => '2024', 'transmission' => 'Automatic', 'fuel_type' => 'Gasoline'],
            ['make' => 'BYD', 'model' => 'eMAX (PHEV MPV)', 'year' => '2024', 'transmission' => 'Automatic', 'fuel_type' => 'PHEV'],
        ];

        $created = 0;
        foreach ($cars as $car) {
            $make = VehicleMake::firstOrCreate([
                'name' => $car['make'],
                'vehicle_type' => 'car'
            ]);

            $model = VehicleModel::firstOrCreate([
                'make_id' => $make->id,
                'name' => $car['model']
            ]);

            if ($model->wasRecentlyCreated) {
                $created++;
            }
        }

        $this->info("Created {$created} car models");
    }

    private function populateMotorcycleData()
    {
        $this->info('Populating motorcycle data...');

        $motorcycles = [
            ['make' => 'Honda', 'model' => 'Giorno+', 'year' => '2025', 'transmission' => 'CVT (scooter)', 'fuel_type' => 'Gasoline'],
            ['make' => 'Honda', 'model' => 'CUV e:', 'year' => '2025', 'transmission' => 'CVT (electric)', 'fuel_type' => 'Electric'],
            ['make' => 'Honda', 'model' => 'ADV350', 'year' => '2025', 'transmission' => 'CVT', 'fuel_type' => 'Gasoline'],
            ['make' => 'Honda', 'model' => 'CB1000 Hornet', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'Honda', 'model' => 'Gold Wing', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'Honda', 'model' => 'Navi', 'year' => '2025', 'transmission' => 'V-Matic (CVT)', 'fuel_type' => 'Gasoline'],
            ['make' => 'SYM', 'model' => 'Sport Rider 125i', 'year' => '2016', 'transmission' => '4-speed rotary', 'fuel_type' => 'Gasoline'],
            ['make' => 'Yamaha', 'model' => 'SZ‑x', 'year' => '2015', 'transmission' => '5-speed manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'Yamaha', 'model' => 'RS‑100T', 'year' => '1977', 'transmission' => '4-speed manual', 'fuel_type' => 'Gasoline (2-stroke)'],
            ['make' => 'Yamaha', 'model' => 'NMAX', 'year' => '2015', 'transmission' => 'CVT', 'fuel_type' => 'Gasoline'],
            ['make' => 'NWOW', 'model' => 'V15', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'BMW', 'model' => 'F 800 GS', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'BMW', 'model' => 'CE 02', 'year' => '2025', 'transmission' => 'Electric (EV)', 'fuel_type' => 'Electric'],
            ['make' => 'Suzuki', 'model' => 'Gixxer SF 155', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'Aprilia', 'model' => 'RS 457', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'Hero', 'model' => 'Hunk', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'CFMoto', 'model' => '250SR Lite', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'CFMoto', 'model' => '250NK Lite', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'Triumph', 'model' => 'Bobber Icon', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'Triumph', 'model' => 'Tiger Sport 800', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'Triumph', 'model' => 'Speed Twin 1200 RS', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'Bristol/Zontes', 'model' => '400E/D/G/M, 703F', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'KTM', 'model' => '390 Adv R / Enduro R / Adv X / SMC R', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'KTM', 'model' => '890 SMT', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'ZEEHO', 'model' => 'MO.1, EZ3, AE4 SE', 'year' => '2025', 'transmission' => 'CVT (electric)', 'fuel_type' => 'Electric'],
            ['make' => 'Royal Enfield', 'model' => 'Bear 650', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'Royal Enfield', 'model' => 'Classic 650', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'Royal Enfield', 'model' => 'Guerrilla 450', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'Royal Enfield', 'model' => 'Shotgun 650', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'Voge', 'model' => 'CU525', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'BMW', 'model' => 'R12', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'BMW', 'model' => 'R12 S / R12 nineT', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'BMW', 'model' => 'M 1000 XR', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'Indian', 'model' => 'FTR series', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'Honda', 'model' => 'Click 125', 'year' => '2020', 'transmission' => 'CVT', 'fuel_type' => 'Gasoline'],
            ['make' => 'Suzuki', 'model' => 'Burgman Street EX', 'year' => '2020', 'transmission' => 'CVT', 'fuel_type' => 'Gasoline'],
            ['make' => 'Vespa', 'model' => 'S125', 'year' => '2020', 'transmission' => 'CVT', 'fuel_type' => 'Gasoline'],
            ['make' => 'Suzuki', 'model' => 'Skydrive', 'year' => '2020', 'transmission' => 'CVT', 'fuel_type' => 'Gasoline'],
            ['make' => 'Suzuki', 'model' => 'Avenis', 'year' => '2020', 'transmission' => 'CVT', 'fuel_type' => 'Gasoline'],
            ['make' => 'Haojue', 'model' => 'Lindy', 'year' => '2020', 'transmission' => 'CVT', 'fuel_type' => 'Gasoline'],
            ['make' => 'Keeway', 'model' => 'CR152', 'year' => '2020', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'Rusi', 'model' => 'Classic 250i', 'year' => '2020', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'Motorstar', 'model' => 'Cafe 400', 'year' => '2020', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
        ];

        $created = 0;
        foreach ($motorcycles as $motorcycle) {
            $make = VehicleMake::firstOrCreate([
                'name' => $motorcycle['make'],
                'vehicle_type' => 'motorcycle'
            ]);

            $model = VehicleModel::firstOrCreate([
                'make_id' => $make->id,
                'name' => $motorcycle['model']
            ]);

            if ($model->wasRecentlyCreated) {
                $created++;
            }
        }

        $this->info("Created {$created} motorcycle models");
    }
}
