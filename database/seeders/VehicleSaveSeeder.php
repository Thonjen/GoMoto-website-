<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleSave;
use Illuminate\Database\Seeder;

class VehicleSaveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users with renter role
        $renters = User::whereHas('role', function ($q) {
            $q->where('name', 'renter');
        })->get();

        // Get available vehicles
        $vehicles = Vehicle::where('is_available', true)
            ->where('status', 'approved')
            ->get();

        if ($renters->isEmpty() || $vehicles->isEmpty()) {
            $this->command->warn('No renters or vehicles found. Skipping vehicle save seeding.');
            return;
        }

        // Create various wishlists and saves
        $wishlistTypes = [
            'My Saved Vehicles',
            'Weekend Trip Cars',
            'Motorcycle Adventures',
            'Family Vehicles',
            'Budget Options',
            'Luxury Rides'
        ];

        $saveNotes = [
            'Perfect for weekend trips',
            'Great fuel efficiency',
            'Spacious and comfortable',
            'Affordable daily rate',
            'Excellent reviews',
            'Close to my location',
            'Looks well maintained',
            'Good for city driving',
            'Adventure ready',
            'Luxury option for special occasions'
        ];

        foreach ($renters as $renter) {
            // Each renter gets 2-4 random wishlists
            $userWishlists = collect($wishlistTypes)->random(rand(2, 4));
            
            foreach ($userWishlists as $listName) {
                // Each wishlist gets 1-X vehicles (where X is max available vehicles)
                $maxVehicles = min(3, $vehicles->count());
                $vehicleCount = rand(1, $maxVehicles);
                $listVehicles = $vehicles->random($vehicleCount);
                
                foreach ($listVehicles as $vehicle) {
                    // Check if already saved to avoid duplicates
                    $exists = VehicleSave::where('user_id', $renter->id)
                        ->where('vehicle_id', $vehicle->id)
                        ->where('list_name', $listName)
                        ->exists();
                        
                    if (!$exists) {
                        VehicleSave::create([
                            'user_id' => $renter->id,
                            'vehicle_id' => $vehicle->id,
                            'list_name' => $listName,
                            'notes' => rand(1, 3) === 1 ? collect($saveNotes)->random() : null, // 1/3 chance of having notes
                            'saved_at' => now()->subDays(rand(0, 30)), // Random save date within last 30 days
                        ]);
                    }
                }
            }
        }

        // Create some popular saves (multiple users saving the same vehicles)
        $popularVehicles = $vehicles->take(2); // Make first 2 vehicles popular
        
        foreach ($popularVehicles as $vehicle) {
            // 60% of renters save these popular vehicles
            $popularSavers = $renters->random(max(1, (int)($renters->count() * 0.6)));
            
            foreach ($popularSavers as $saver) {
                $exists = VehicleSave::where('user_id', $saver->id)
                    ->where('vehicle_id', $vehicle->id)
                    ->where('list_name', 'My Saved Vehicles')
                    ->exists();
                    
                if (!$exists) {
                    VehicleSave::create([
                        'user_id' => $saver->id,
                        'vehicle_id' => $vehicle->id,
                        'list_name' => 'My Saved Vehicles',
                        'notes' => 'Popular choice!',
                        'saved_at' => now()->subDays(rand(0, 7)), // Recent saves
                    ]);
                }
            }
        }

        $totalSaves = VehicleSave::count();
        $this->command->info("Vehicle saves seeded successfully! Created {$totalSaves} saves across multiple wishlists.");
        
        // Show some statistics
        $uniqueUsers = VehicleSave::distinct('user_id')->count('user_id');
        $uniqueLists = VehicleSave::distinct('list_name')->count('list_name');
        $mostSavedVehicle = VehicleSave::select('vehicle_id')
            ->groupBy('vehicle_id')
            ->orderByRaw('COUNT(*) DESC')
            ->with('vehicle.make', 'vehicle.model')
            ->first();
            
        $this->command->info("Statistics:");
        $this->command->info("- {$uniqueUsers} users have saved vehicles");
        $this->command->info("- {$uniqueLists} different wishlists created");
        
        if ($mostSavedVehicle && $mostSavedVehicle->vehicle) {
            $saveCount = VehicleSave::where('vehicle_id', $mostSavedVehicle->vehicle_id)->count();
            $vehicleName = $mostSavedVehicle->vehicle->make->name . ' ' . $mostSavedVehicle->vehicle->model->name;
            $this->command->info("- Most saved vehicle: {$vehicleName} ({$saveCount} saves)");
        }
    }
}
