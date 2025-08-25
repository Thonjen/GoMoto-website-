<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleSave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class VehicleSaveController extends Controller
{
    /**
     * Display user's saved vehicles / wishlists
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $listName = $request->get('list', 'My Saved Vehicles');
        
        // Get all user's wishlists
        $wishlists = VehicleSave::getUserWishlists($user->id);
        
        // Get saved vehicles for the selected list
        $savedVehicles = VehicleSave::with(['vehicle.make', 'vehicle.model', 'vehicle.type', 'vehicle.pricingTiers', 'vehicle.owner'])
            ->forUser($user->id)
            ->inList($listName)
            ->orderBy('saved_at', 'desc')
            ->paginate(12);

        // Get save statistics
        $stats = [
            'total_saves' => VehicleSave::forUser($user->id)->count(),
            'total_lists' => $wishlists->count(),
            'recent_saves' => VehicleSave::forUser($user->id)->recent(7)->count(),
        ];

        return Inertia::render('Renter/SavedVehicles', [
            'savedVehicles' => $savedVehicles,
            'wishlists' => $wishlists,
            'currentList' => $listName,
            'stats' => $stats,
        ]);
    }

    /**
     * Save a vehicle to user's wishlist
     */
    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'list_name' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $vehicleId = $request->vehicle_id;
        $listName = $request->list_name ?: 'My Saved Vehicles';

        // Check if already saved
        $existingSave = VehicleSave::where('user_id', $user->id)
            ->where('vehicle_id', $vehicleId)
            ->where('list_name', $listName)
            ->first();

        if ($existingSave) {
            return response()->json([
                'message' => 'Vehicle is already in your ' . $listName . ' list',
                'saved' => true,
                'save_count' => VehicleSave::getSaveCount($vehicleId)
            ]);
        }

        // Create the save
        VehicleSave::create([
            'user_id' => $user->id,
            'vehicle_id' => $vehicleId,
            'list_name' => $listName,
            'notes' => $request->notes,
            'saved_at' => now(),
        ]);

        return response()->json([
            'message' => 'Vehicle saved to ' . $listName,
            'saved' => true,
            'save_count' => VehicleSave::getSaveCount($vehicleId)
        ]);
    }

    /**
     * Remove a vehicle from user's wishlist
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'list_name' => 'nullable|string',
        ]);

        $user = Auth::user();
        $vehicleId = $request->vehicle_id;
        $listName = $request->list_name ?: 'My Saved Vehicles';

        $deleted = VehicleSave::where('user_id', $user->id)
            ->where('vehicle_id', $vehicleId)
            ->where('list_name', $listName)
            ->delete();

        if ($deleted) {
            return response()->json([
                'message' => 'Vehicle removed from ' . $listName,
                'saved' => false,
                'save_count' => VehicleSave::getSaveCount($vehicleId)
            ]);
        }

        return response()->json([
            'message' => 'Vehicle was not in your ' . $listName . ' list',
            'saved' => false,
            'save_count' => VehicleSave::getSaveCount($vehicleId)
        ], 404);
    }

    /**
     * Toggle save status for a vehicle
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'list_name' => 'nullable|string|max:100',
        ]);

        $user = Auth::user();
        $vehicleId = $request->vehicle_id;
        $listName = $request->list_name ?: 'My Saved Vehicles';

        $existingSave = VehicleSave::where('user_id', $user->id)
            ->where('vehicle_id', $vehicleId)
            ->where('list_name', $listName)
            ->first();

        if ($existingSave) {
            // Remove from saves
            $existingSave->delete();
            return response()->json([
                'message' => 'Vehicle removed from ' . $listName,
                'saved' => false,
                'save_count' => VehicleSave::getSaveCount($vehicleId)
            ]);
        } else {
            // Add to saves
            VehicleSave::create([
                'user_id' => $user->id,
                'vehicle_id' => $vehicleId,
                'list_name' => $listName,
                'saved_at' => now(),
            ]);
            return response()->json([
                'message' => 'Vehicle saved to ' . $listName,
                'saved' => true,
                'save_count' => VehicleSave::getSaveCount($vehicleId)
            ]);
        }
    }

    /**
     * Check if a vehicle is saved by the current user
     */
    public function check(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'list_name' => 'nullable|string',
        ]);

        $user = Auth::user();
        $vehicleId = $request->vehicle_id;
        $listName = $request->list_name ?: 'My Saved Vehicles';

        $isSaved = VehicleSave::isSaved($user->id, $vehicleId, $listName);

        return response()->json([
            'saved' => $isSaved,
            'save_count' => VehicleSave::getSaveCount($vehicleId)
        ]);
    }

    /**
     * Get popular/most saved vehicles
     */
    public function popular()
    {
        $popularVehicles = VehicleSave::getMostSaved(10);

        return response()->json([
            'vehicles' => $popularVehicles
        ]);
    }

    /**
     * Create a new wishlist
     */
    public function createList(Request $request)
    {
        $request->validate([
            'list_name' => 'required|string|max:100|unique:vehicle_saves,list_name,NULL,id,user_id,' . Auth::id(),
        ]);

        return response()->json([
            'message' => 'Wishlist "' . $request->list_name . '" is ready to use',
            'list_name' => $request->list_name
        ]);
    }

    /**
     * Move vehicle to different wishlist
     */
    public function moveToList(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'from_list' => 'required|string',
            'to_list' => 'required|string|max:100',
        ]);

        $user = Auth::user();

        $save = VehicleSave::where('user_id', $user->id)
            ->where('vehicle_id', $request->vehicle_id)
            ->where('list_name', $request->from_list)
            ->first();

        if (!$save) {
            return response()->json([
                'message' => 'Vehicle not found in source list'
            ], 404);
        }

        // Check if already exists in target list
        $existsInTarget = VehicleSave::where('user_id', $user->id)
            ->where('vehicle_id', $request->vehicle_id)
            ->where('list_name', $request->to_list)
            ->exists();

        if ($existsInTarget) {
            return response()->json([
                'message' => 'Vehicle already exists in target list'
            ], 422);
        }

        // Update the list name
        $save->update(['list_name' => $request->to_list]);

        return response()->json([
            'message' => 'Vehicle moved to ' . $request->to_list
        ]);
    }

    /**
     * Delete a wishlist and move all vehicles to default list
     */
    public function deleteList(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'list_name' => 'required|string|max:100',
        ]);
        
        $listName = $request->list_name;
        
        // Prevent deletion of default list
        if ($listName === 'My Saved Vehicles') {
            return response()->json([
                'message' => 'Cannot delete the default list'
            ], 422);
        }
        
        // Move all vehicles from this list to default list
        VehicleSave::where('user_id', $user->id)
            ->where('list_name', $listName)
            ->update(['list_name' => 'My Saved Vehicles']);
        
        return response()->json([
            'message' => 'List deleted and vehicles moved to default list'
        ]);
    }
}
