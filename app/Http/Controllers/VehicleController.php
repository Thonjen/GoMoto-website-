<?php
namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Brand;
use App\Models\VehicleType;
use App\Models\FuelType;
use App\Models\VehiclePhoto;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Auth::user()
            ->vehicles()
            ->with(['brand', 'type', 'fuelType', 'photos'])
            ->latest()
            ->paginate(10);

        return Inertia::render('Owner/Vehicle/Index', [
            'vehicles' => $vehicles,
        ]);
    }

    public function create()
    {
        return Inertia::render('Owner/Vehicle/Create', [
            'brands' => Brand::all(),
            'types' => VehicleType::all(),
            'fuelTypes' => FuelType::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'license_plate' => 'required|unique:vehicles',
            'brand_id' => 'required|exists:brands,id',
            'type_id' => 'required|exists:vehicle_types,id',
            'fuel_type_id' => 'required|exists:fuel_types,id',
            'year' => 'required|integer',
            'color' => 'required|string',
            'is_available' => 'boolean',
            'description' => 'nullable|string',
        ]);

        $vehicle = Auth::user()->vehicles()->create([
            'license_plate' => $request->license_plate,
            'brand_id' => $request->brand_id,
            'type_id' => $request->type_id,
            'fuel_type_id' => $request->fuel_type_id,
            'year' => $request->year,
            'color' => $request->color,
            'is_available' => $request->is_available ?? true,
            'description' => $request->description,
        ]);

        return redirect()->route('vehicles.index')->with('success', 'Vehicle created.');
    }

    public function show(Vehicle $vehicle)
    {
        if ($vehicle->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        $vehicle->load(['brand', 'type', 'fuelType', 'photos']);

        return Inertia::render('Owner/Vehicle/Show', [
            'vehicle' => $vehicle,
            'brands' => Brand::all(),
            'types' => VehicleType::all(),
            'fuelTypes' => FuelType::all(),
        ]);
    }

    public function edit(Vehicle $vehicle)
    {
        if ($vehicle->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        $vehicle->load(['brand', 'type', 'fuelType', 'photos']);

        return Inertia::render('Owner/Vehicle/Edit', [
            'vehicle' => $vehicle,
            'brands' => Brand::all(),
            'types' => VehicleType::all(),
            'fuelTypes' => FuelType::all(),
        ]);
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        if ($vehicle->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'license_plate' => "required|unique:vehicles,license_plate,{$vehicle->id}",
            'brand_id' => 'required|exists:brands,id',
            'type_id' => 'required|exists:vehicle_types,id',
            'fuel_type_id' => 'required|exists:fuel_types,id',
            'year' => 'required|integer',
            'color' => 'required|string',
            'is_available' => 'boolean',
            'description' => 'nullable|string',
        ]);

        $vehicle->update($request->only([
            'license_plate', 'brand_id', 'type_id', 'fuel_type_id', 'year', 'color', 'is_available', 'description'
        ]));

        return back()->with('success', 'Vehicle updated.');
    }

    public function destroy(Vehicle $vehicle)
    {
        if ($vehicle->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        $vehicle->delete();

        return redirect()->route('vehicles.index')->with('success', 'Vehicle deleted.');
    }

    public function uploadPhotos(Request $request, Vehicle $vehicle)
    {
        if ($vehicle->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'photos' => 'required|array',
            'photos.*' => 'required|image|max:2048', // 2048 KB = 2 MB
        ], [
            'photos.*.max' => 'Each photo must not be greater than 2MB.',
        ]);

        $uploaded = [];
        foreach ($request->file('photos', []) as $photo) {
            $path = $photo->store('vehicles', 'public');
            $photoModel = VehiclePhoto::create([
                'vehicle_id' => $vehicle->id,
                'url' => "/storage/$path",
            ]);
            $uploaded[] = $photoModel;
        }

        return response()->json(['photos' => $uploaded]);
    }

    public function deletePhoto(VehiclePhoto $photo)
    {
        $photo->load('vehicle'); // Ensure vehicle is loaded
        if ($photo->vehicle->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        $photo->delete();

        return back()->with('success', 'Photo deleted.');
    }
}
