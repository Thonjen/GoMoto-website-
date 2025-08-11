<?php

namespace App\Console\Commands;

use App\Models\VehicleMake;
use App\Models\VehicleModel;
use App\Models\FuelType;
use App\Models\Transmission;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PopulateVehicleData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vehicle-data:populate {--force : Force repopulation even if data exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate vehicle makes and models from NHTSA API, and create basic fuel types and transmissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting vehicle data population...');

        // Create basic fuel types
        $this->populateFuelTypes();
        
        // Create basic transmissions
        $this->populateTransmissions();

        // Populate car makes
        $this->populateCarMakes();

        // Populate motorcycle makes  
        $this->populateMotorcycleMakes();

        // Populate models for popular makes
        $this->populatePopularModels();

        $this->info('Vehicle data population completed!');
    }

    private function populateFuelTypes()
    {
        $this->info('Creating fuel types...');
        
        $fuelTypes = [
            'Gasoline',
            'Diesel', 
            'Electric',
            'Hybrid',
            'CNG', // Compressed Natural Gas
            'LPG'  // Liquefied Petroleum Gas
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
            ['name' => 'Manual', 'description' => 'Manual transmission (stick shift)'],
            ['name' => 'Automatic', 'description' => 'Automatic transmission'],
            ['name' => 'CVT', 'description' => 'Continuously Variable Transmission'],
            ['name' => 'Semi-Automatic', 'description' => 'Semi-automatic transmission'],
        ];

        foreach ($transmissions as $transmission) {
            Transmission::firstOrCreate(
                ['name' => $transmission['name']],
                $transmission
            );
        }

        $this->info('Transmission types created.');
    }

    private function populateCarMakes()
    {
        $this->info('Fetching car makes from NHTSA...');

        try {
            $response = Http::timeout(30)->get('https://vpic.nhtsa.dot.gov/api/vehicles/GetMakesForVehicleType/car?format=json');
            
            if (!$response->successful()) {
                $this->error('Failed to fetch car makes from NHTSA API');
                return;
            }

            $data = $response->json();
            $makes = $data['Results'] ?? [];
            $created = 0;

            foreach ($makes as $make) {
                $created += VehicleMake::firstOrCreate([
                    'name' => $make['MakeName'],
                    'vehicle_type' => 'car'
                ], [
                    'make_id' => $make['MakeId']
                ])->wasRecentlyCreated ? 1 : 0;
            }

            $this->info("Created {$created} new car makes (total: " . count($makes) . ")");

        } catch (\Exception $e) {
            $this->error('Error fetching car makes: ' . $e->getMessage());
        }
    }

    private function populateMotorcycleMakes()
    {
        $this->info('Fetching motorcycle makes from NHTSA...');

        try {
            $response = Http::timeout(30)->get('https://vpic.nhtsa.dot.gov/api/vehicles/GetMakesForVehicleType/motorcycle?format=json');
            
            if (!$response->successful()) {
                $this->error('Failed to fetch motorcycle makes from NHTSA API');
                return;
            }

            $data = $response->json();
            $makes = $data['Results'] ?? [];
            $created = 0;

            foreach ($makes as $make) {
                $created += VehicleMake::firstOrCreate([
                    'name' => $make['MakeName'],
                    'vehicle_type' => 'motorcycle'
                ], [
                    'make_id' => $make['MakeId']
                ])->wasRecentlyCreated ? 1 : 0;
            }

            $this->info("Created {$created} new motorcycle makes (total: " . count($makes) . ")");

        } catch (\Exception $e) {
            $this->error('Error fetching motorcycle makes: ' . $e->getMessage());
        }
    }

    private function populatePopularModels()
    {
        $this->info('Populating models for popular makes...');

        // Popular car makes
        $popularCarMakes = ['Honda', 'Toyota', 'Ford', 'Chevrolet', 'Nissan', 'Hyundai', 'Kia', 'Volkswagen'];
        
        // Popular motorcycle makes
        $popularMotorcycleMakes = ['Honda', 'Yamaha', 'Kawasaki', 'Suzuki', 'Harley-Davidson'];

        $allPopularMakes = array_merge($popularCarMakes, $popularMotorcycleMakes);

        foreach ($allPopularMakes as $makeName) {
            $make = VehicleMake::where('name', $makeName)->first();
            if ($make) {
                $this->populateModelsForMake($make);
                sleep(1); // Be nice to the API
            }
        }
    }

    private function populateModelsForMake(VehicleMake $make)
    {
        $this->info("Fetching models for {$make->name}...");

        try {
            $response = Http::timeout(30)->get("https://vpic.nhtsa.dot.gov/api/vehicles/GetModelsForMake/{$make->name}?format=json");
            
            if (!$response->successful()) {
                $this->warn("Failed to fetch models for {$make->name}");
                return;
            }

            $data = $response->json();
            $models = $data['Results'] ?? [];
            $created = 0;

            foreach ($models as $model) {
                $created += VehicleModel::firstOrCreate([
                    'make_id' => $make->id,
                    'name' => $model['Model_Name']
                ], [
                    'model_id' => $model['Model_ID'] ?? null
                ])->wasRecentlyCreated ? 1 : 0;
            }

            $this->info("  Created {$created} new models for {$make->name}");

        } catch (\Exception $e) {
            $this->warn("Error fetching models for {$make->name}: " . $e->getMessage());
        }
    }
}
