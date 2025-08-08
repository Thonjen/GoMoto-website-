<?php

namespace App\Http\Controllers;

use App\Models\{Vehicle, Brand, VehicleType, FuelType, VehiclePhoto, Booking};
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Auth::user()
            ->vehicles()
            ->with(['brand', 'type', 'fuelType', 'photos', 'pricingTiers']) // <-- add pricingTiers
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
    if (is_string($request->pricing_tier_ids)) {
        $request->merge([
            'pricing_tier_ids' => json_decode($request->pricing_tier_ids, true),
        ]);
    }

        // Log all incoming request data (except files)
        Log::info('Vehicle Store Request Data:', $request->except(['main_photo']));

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
            'location_name' => 'required|string',
            'pricing_tiers' => 'array',
            'pricing_tiers.*.duration_from' => 'required|integer|min:1',
            'pricing_tiers.*.duration_unit' => 'required|in:minutes,hours,days',
            'pricing_tiers.*.price' => 'required|numeric|min:0',
            'pricing_tier_ids' => 'array',
            'pricing_tier_ids.*' => 'exists:vehicle_pricing_tiers,id',
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
            'location_name' => $request->input('location_name'),
        ]);

        // ✅ Log entire created model
        Log::debug('Created Vehicle:', $vehicle->toArray());

        // Attach selected pricing tiers
        if ($request->has('pricing_tier_ids')) {
            $vehicle->pricingTiers()->sync($request->input('pricing_tier_ids'));

            // ✅ Log synced tier IDs
            Log::debug('Attached Pricing Tier IDs:', $request->input('pricing_tier_ids'));
        }

        Log::info('Vehicle created successfully with ID: ' . $vehicle->id);

        return redirect()->route('vehicles.index')->with('success', 'Vehicle created.');
    }


    public function show(Vehicle $vehicle)
    {
        if ($vehicle->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $vehicle->load(['brand', 'type', 'fuelType', 'photos', 'pricingTiers']); // <-- add pricingTiers

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

        $vehicle->load(['brand', 'type', 'fuelType', 'photos', 'pricingTiers']); // <-- add pricingTiers

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
            'location_name' => 'required|string',
            'pricing_tiers' => 'array',
            'pricing_tiers.*.duration_from' => 'required|integer|min:1',
            'pricing_tiers.*.duration_unit' => 'required|in:minutes,hours,days',
            'pricing_tiers.*.price' => 'required|numeric|min:0',
            'pricing_tier_ids' => 'array',
            'pricing_tier_ids.*' => 'exists:vehicle_pricing_tiers,id',
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
            'location_name' => $request->input('location_name'),
        ];

        if ($request->hasFile('main_photo')) {
            $data['main_photo_url'] = '/storage/' . $request->file('main_photo')->store('vehicles/main', 'public');
        }

        $vehicle->update($data);

        // Sync selected pricing tiers
        if ($request->has('pricing_tier_ids')) {
            $vehicle->pricingTiers()->sync($request->input('pricing_tier_ids'));
        }

        Log::info('Updated vehicle #' . $vehicle->id, $data);

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
            'photos.*' => 'required|image|max:2048',
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
        $photo->load('vehicle');
        if ($photo->vehicle->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $photo->delete();

        return back()->with('success', 'Photo deleted.');
    }

    public function publicIndex(Request $request)
    {
        $query = Vehicle::with(['brand', 'type', 'fuelType', 'pricingTiers']) // <-- add pricingTiers
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

        foreach ($vehicles as $vehicle) {
            $activeBooking = Booking::where('vehicle_id', $vehicle->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->where('pickup_datetime', '>=', now())
                ->first();
            $vehicle->is_booked = $activeBooking ? true : false;
        }

        return Inertia::render('Public/Vehicles', [
            'vehicles' => $vehicles,
            'brands' => Brand::all(),
            'types' => VehicleType::all(),
            'fuelTypes' => FuelType::all(),
            'filters' => $request->only(['brand_id', 'fuel_type_id', 'type_id', 'color']),
        ]);
    }

    // Owner booking requests page
    public function bookingRequests()
    {
        $ownerId = Auth::id();
        $bookings = Booking::with(['vehicle', 'user'])
            ->whereHas('vehicle', function ($q) use ($ownerId) {
                $q->where('owner_id', $ownerId);
            })
            ->latest()
            ->get();

        return Inertia::render('Owner/BookingRequests', [
            'bookingRequests' => $bookings,
        ]);
    }

    // Owner review single booking
    public function showBooking($id)
    {
        $booking = Booking::with(['vehicle', 'user'])->findOrFail($id);
        if ($booking->vehicle->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        return Inertia::render('Owner/ConfirmBooking', [
            'booking' => $booking,
        ]);
    }

    // Confirm booking (owner action)
    public function confirmBooking($id)
    {
        $booking = Booking::with('vehicle')->findOrFail($id);
        if ($booking->vehicle->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        $booking->status = 'confirmed';
        $booking->save();

        // Mark vehicle as unavailable (booked)
        $vehicle = $booking->vehicle;
        $vehicle->is_available = false;
        $vehicle->save();

        return back()->with('success', 'Booking confirmed and vehicle marked as booked.');
    }

    // Reject booking (owner action)
    public function rejectBooking($id)
    {
        $booking = Booking::with('vehicle')->findOrFail($id);
        if ($booking->vehicle->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        $booking->status = 'rejected';
        $booking->save();

        // Optionally, make vehicle available again
        $vehicle = $booking->vehicle;
        $vehicle->is_available = true;
        $vehicle->save();

        return back()->with('success', 'Booking rejected.');
    }

    // API endpoint to get owner's vehicles and their pricing tiers for reuse
    public function pricingTiersForOwner()
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $vehicles = Auth::user()->vehicles()
            ->with(['pricingTiers'])
            ->get();

        return response()->json([
            'pricingTiers' => $vehicles->flatMap->pricingTiers,
        ]);
    }

    public function publicShow(Vehicle $vehicle)
    {
        $vehicle->load(['brand', 'type', 'fuelType', 'photos', 'owner', 'pricingTiers']);

        // Check if vehicle has any pending/confirmed bookings
        $activeBooking = \App\Models\Booking::where('vehicle_id', $vehicle->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->first();
        $vehicle->is_booked = $activeBooking ? true : false;

        // Check if current user has any active bookings for this vehicle
        $userActiveBookings = [];
        if (Auth::check()) {
            $userActiveBookings = \App\Models\Booking::getUserActiveBookingsForVehicle(Auth::id(), $vehicle->id);
        }

        return \Inertia\Inertia::render('Public/VehicleDetail', [
            'vehicle' => $vehicle,
            'userActiveBookings' => $userActiveBookings,
        ]);
    }
}
