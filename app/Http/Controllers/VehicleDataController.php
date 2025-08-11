<?php

namespace App\Http\Controllers;

use App\Models\VehicleMake;
use App\Models\VehicleModel;
use App\Models\VehicleType;
use App\Models\FuelType;
use App\Models\Transmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class VehicleDataController extends Controller
{
    /**
     * Get all vehicle types
     */
    public function getVehicleTypes()
    {
        $types = VehicleType::orderBy('category')->orderBy('sub_type')->get();
        return response()->json($types);
    }

    /**
     * Get makes by vehicle type (car or motorcycle)
     */
    public function getMakesByType($vehicleType)
    {
        $makes = VehicleMake::forVehicleType($vehicleType)
            ->orderedByName()
            ->get();
        
        return response()->json($makes);
    }

    /**
     * Get models by make ID
     */
    public function getModelsByMake($makeId)
    {
        $models = VehicleModel::forMake($makeId)
            ->orderedByName()
            ->get();
        
        return response()->json($models);
    }

    /**
     * Get all fuel types
     */
    public function getFuelTypes()
    {
        $fuelTypes = FuelType::orderBy('name')->get();
        return response()->json($fuelTypes);
    }

    /**
     * Get all transmissions
     */
    public function getTransmissions()
    {
        $transmissions = Transmission::orderedByName()->get();
        return response()->json($transmissions);
    }

    /**
     * Generate years from 2000 to current year + 1
     */
    public function getYears()
    {
        $currentYear = date('Y');
        $years = [];
        
        for ($year = 2000; $year <= $currentYear + 1; $year++) {
            $years[] = $year;
        }
        
        return response()->json(array_reverse($years)); // Latest first
    }

    /**
     * Populate Philippine vehicle data (replaces NHTSA API)
     */
    public function populatePhilippineData()
    {
        try {
            // Run the Philippine vehicle data population command
            Artisan::call('vehicle-data:populate-ph', ['--fresh' => true]);
            
            $output = Artisan::output();
            Log::info('Philippine vehicle data populated: ' . $output);
            
            // Get updated statistics
            $stats = [
                'carMakes' => VehicleMake::where('vehicle_type', 'car')->count(),
                'motorcycleMakes' => VehicleMake::where('vehicle_type', 'motorcycle')->count(),
                'totalModels' => VehicleModel::count(),
                'fuelTypes' => FuelType::count(),
                'transmissions' => Transmission::count(),
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Successfully populated Philippine vehicle data',
                'stats' => $stats,
                'output' => $output
            ]);

        } catch (\Exception $e) {
            Log::error('Error populating Philippine vehicle data: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to populate Philippine vehicle data'], 500);
        }
    }

    /**
     * Populate makes for cars (uses Philippine data)
     */
    public function populateCarMakes()
    {
        return $this->populatePhilippineData();
    }

    /**
     * Populate makes for motorcycles (uses Philippine data)
     */
    public function populateMotorcycleMakes()
    {
        return $this->populatePhilippineData();
    }

    /**
     * Populate models for a specific make (uses Philippine data)
     */
    public function populateModelsForMake(Request $request, $makeId)
    {
        try {
            $make = VehicleMake::findOrFail($makeId);
            
            // Check if this make already has models
            $existingModels = VehicleModel::where('make_id', $makeId)->count();
            
            if ($existingModels > 0) {
                return response()->json([
                    'success' => true,
                    'message' => "Make '{$make->name}' already has {$existingModels} models",
                    'total_models' => $existingModels
                ]);
            }
            
            // For Philippine data, we populate all data at once
            // So if a make exists but has no models, we run the full population
            return $this->populatePhilippineData();

        } catch (\Exception $e) {
            Log::error("Error populating models for make {$makeId}: " . $e->getMessage());
            return response()->json(['error' => 'Failed to populate models'], 500);
        }
    }

    /**
     * Add a new make manually
     */
    public function addMake(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'vehicle_type' => 'required|in:car,motorcycle'
        ]);

        $make = VehicleMake::firstOrCreate([
            'name' => $request->name,
            'vehicle_type' => $request->vehicle_type
        ]);

        return response()->json([
            'success' => true,
            'make' => $make,
            'message' => $make->wasRecentlyCreated ? 'Make added successfully' : 'Make already exists'
        ]);
    }

    /**
     * Add a new model manually
     */
    public function addModel(Request $request)
    {
        $request->validate([
            'make_id' => 'required|exists:vehicle_makes,id',
            'name' => 'required|string|max:100'
        ]);

        $model = VehicleModel::firstOrCreate([
            'make_id' => $request->make_id,
            'name' => $request->name
        ]);

        return response()->json([
            'success' => true,
            'model' => $model->load('make'),
            'message' => $model->wasRecentlyCreated ? 'Model added successfully' : 'Model already exists'
        ]);
    }

    /**
     * Add a new fuel type manually
     */
    public function addFuelType(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:fuel_types,name'
        ]);

        $fuelType = FuelType::create([
            'name' => $request->name
        ]);

        return response()->json([
            'success' => true,
            'fuel_type' => $fuelType,
            'message' => 'Fuel type added successfully'
        ]);
    }

    /**
     * Add a new transmission manually
     */
    public function addTransmission(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:transmissions,name',
            'description' => 'nullable|string'
        ]);

        $transmission = Transmission::create([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return response()->json([
            'success' => true,
            'transmission' => $transmission,
            'message' => 'Transmission added successfully'
        ]);
    }

    /**
     * Search makes by name
     */
    public function searchMakes(Request $request)
    {
        $query = $request->get('q', '');
        $vehicleType = $request->get('vehicle_type');

        $makes = VehicleMake::where('name', 'LIKE', "%{$query}%")
            ->when($vehicleType, function ($q) use ($vehicleType) {
                return $q->where('vehicle_type', $vehicleType);
            })
            ->orderedByName()
            ->limit(20)
            ->get();

        return response()->json($makes);
    }

    /**
     * Search models by name
     */
    public function searchModels(Request $request)
    {
        $query = $request->get('q', '');
        $makeId = $request->get('make_id');

        $models = VehicleModel::where('name', 'LIKE', "%{$query}%")
            ->when($makeId, function ($q) use ($makeId) {
                return $q->where('make_id', $makeId);
            })
            ->with('make')
            ->orderedByName()
            ->limit(20)
            ->get();

        return response()->json($models);
    }

    /**
     * Get vehicle data statistics
     */
    public function stats()
    {
        $stats = [
            'carMakes' => VehicleMake::where('vehicle_type', 'car')->count(),
            'motorcycleMakes' => VehicleMake::where('vehicle_type', 'motorcycle')->count(),
            'models' => VehicleModel::count(),
            'fuelTypes' => FuelType::count(),
            'transmissions' => Transmission::count(),
        ];

        return Inertia::render('Owner/VehicleDataStats', [
            'stats' => $stats
        ]);
    }
}
