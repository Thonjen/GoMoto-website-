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
            'main_photo' => 'nullable|image|max:4096',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $mainPhotoUrl = null;
        if ($request->hasFile('main_photo')) {
            $mainPhotoUrl = '/storage/' . $request->file('main_photo')->store('vehicles/main', 'public');
        }

        $vehicle = Auth::user()->vehicles()->create([
            'license_plate' => $request->input('license_plate'),
            'brand_id' => (int) $request->input('brand_id'),
            'type_id' => (int) $request->input('type_id'),
            'fuel_type_id' => (int) $request->input('fuel_type_id'),
            'year' => (int) $request->input('year'),
            'color' => $request->input('color'),
            'is_available' => $request->has('is_available') ? (bool)$request->input('is_available') : false,
            'description' => $request->input('description'),
            'main_photo_url' => $mainPhotoUrl,
            'lat' => (float) $request->input('lat'),
            'lng' => (float) $request->input('lng'),
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
            'main_photo' => 'nullable|image|max:4096',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $data = [
            'license_plate' => $request->input('license_plate'),
            'brand_id' => (int) $request->input('brand_id'),
            'type_id' => (int) $request->input('type_id'),
            'fuel_type_id' => (int) $request->input('fuel_type_id'),
            'year' => (int) $request->input('year'),
            'color' => $request->input('color'),
            'is_available' => $request->has('is_available') ? (bool)$request->input('is_available') : false,
            'description' => $request->input('description'),
            'lat' => (float) $request->input('lat'),
            'lng' => (float) $request->input('lng'),
        ];

        if ($request->hasFile('main_photo')) {
            $data['main_photo_url'] = '/storage/' . $request->file('main_photo')->store('vehicles/main', 'public');
        }

        $vehicle->update($data);

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

    public function publicIndex(Request $request)
    {
        $query = Vehicle::with(['brand', 'type', 'fuelType'])
            ->where('is_available', true);

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }
        if ($request->filled('fuel_type_id')) {
            $query->where('fuel_type_id', $request->fuel_type_id);
        }
        if ($request->filled('type_id')) {
            $query->where('type_id', $request->type_id);
        }
        if ($request->filled('color')) {
            $query->where('color', 'like', '%' . $request->color . '%');
        }

        $vehicles = $query->latest()->paginate(9)->withQueryString();

        return Inertia::render('Public/Vehicles', [
            'vehicles' => $vehicles,
            'brands' => Brand::all(),
            'types' => VehicleType::all(),
            'fuelTypes' => FuelType::all(),
            'filters' => $request->only(['brand_id', 'fuel_type_id', 'type_id', 'color']),
        ]);
    }
}
