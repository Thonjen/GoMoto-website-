<?php

namespace App\Http\Controllers;

use App\Models\{Vehicle, VehicleType, FuelType, VehiclePhoto, Booking, VehicleMake, VehicleModel, Transmission, VehiclePricingTier};
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Auth::user()
            ->vehicles()
            ->with(['make', 'model', 'type', 'fuelType', 'photos', 'pricingTiers', 'transmission', 'owner'])
            ->latest()
            ->paginate(10);

        return Inertia::render('Owner/Vehicle/Index', [
            'vehicles' => $vehicles,
        ]);
    }

    public function create()
    {
        return Inertia::render('Owner/Vehicle/Create', [
            'fuelTypes' => FuelType::orderBy('name')->get(),
            'transmissions' => Transmission::orderBy('name')->get(),
            'types' => VehicleType::orderBy('category')->orderBy('sub_type')->get(),
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
            'license_plate' => 'nullable|unique:vehicles',
            'make_id' => 'required|exists:vehicle_makes,id',
            'model_id' => 'required|exists:vehicle_models,id',
            'type_id' => 'required|exists:vehicle_types,id',
            'fuel_type_id' => 'required|exists:fuel_types,id',
            'transmission_id' => 'required|exists:transmissions,id',
            'year' => 'required|integer|min:2000|max:' . (date('Y') + 2),
            'color' => 'required|string|max:50',
            'description' => 'nullable|string',
            'main_photo' => 'nullable|image|max:4096',
            'location_name' => 'required|string',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'pricing_tier_ids' => 'array',
            'pricing_tier_ids.*' => 'exists:vehicle_pricing_tiers,id',
        ]);

        $mainPhotoUrl = null;
        if ($request->hasFile('main_photo')) {
            $mainPhotoUrl = '/storage/' . $request->file('main_photo')->store('vehicles/main', 'public');
        }

        $vehicle = Auth::user()->vehicles()->create([
            'license_plate' => $request->input('license_plate'),
            'make_id' => (int) $request->input('make_id'),
            'model_id' => (int) $request->input('model_id'),
            'type_id' => (int) $request->input('type_id'),
            'fuel_type_id' => (int) $request->input('fuel_type_id'),
            'transmission_id' => (int) $request->input('transmission_id'),
            'year' => (int) $request->input('year'),
            'color' => $request->input('color'),
            'is_available' => $request->has('is_available') ? (bool)$request->input('is_available') : true,
            'status' => 'approved', // Auto-approve vehicles upon creation
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

        return redirect()->route('owner.vehicles.index')->with('success', 'Vehicle created.');
    }


    public function show(Vehicle $vehicle)
    {
        if ($vehicle->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $vehicle->load(['make', 'model', 'type', 'fuelType', 'photos', 'pricingTiers', 'transmission', 'owner']); 

        return Inertia::render('Owner/Vehicle/Show', [
            'vehicle' => $vehicle,
            'makes' => VehicleMake::orderBy('name')->get(),
            'types' => VehicleType::orderBy('category')->orderBy('sub_type')->get(),
            'fuelTypes' => FuelType::orderBy('name')->get(),
            'transmissions' => Transmission::orderBy('name')->get(),
        ]);
    }

    public function edit(Vehicle $vehicle)
    {
        if ($vehicle->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $vehicle->load(['make', 'model', 'type', 'fuelType', 'photos', 'pricingTiers', 'transmission', 'owner']); 

        return Inertia::render('Owner/Vehicle/Edit', [
            'vehicle' => $vehicle,
            'makes' => VehicleMake::orderBy('name')->get(),
            'models' => $vehicle->make_id ? VehicleModel::where('make_id', $vehicle->make_id)->orderBy('name')->get() : [],
            'types' => VehicleType::orderBy('category')->orderBy('sub_type')->get(),
            'fuelTypes' => FuelType::orderBy('name')->get(),
            'transmissions' => Transmission::orderBy('name')->get(),
            'vehicleTypeCategories' => ['car', 'motorcycle'],
        ]);
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        if ($vehicle->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Handle pricing_tier_ids if it's a JSON string
        if (is_string($request->pricing_tier_ids)) {
            $request->merge([
                'pricing_tier_ids' => json_decode($request->pricing_tier_ids, true),
            ]);
        }

        $request->validate([
            'license_plate' => "nullable|unique:vehicles,license_plate,{$vehicle->id}",
            'make_id' => 'required|exists:vehicle_makes,id',
            'model_id' => 'required|exists:vehicle_models,id',
            'type_id' => 'required|exists:vehicle_types,id',
            'fuel_type_id' => 'required|exists:fuel_types,id',
            'transmission_id' => 'required|exists:transmissions,id',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 2),
            'color' => 'required|string|max:50',
            'description' => 'nullable|string',
            'main_photo' => 'nullable|image|max:5120', // 5MB
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'location_name' => 'required|string',
            'pricing_tier_ids' => 'array',
            'pricing_tier_ids.*' => 'exists:vehicle_pricing_tiers,id',
        ]);

        $data = [
            'license_plate' => $request->input('license_plate'),
            'make_id' => (int) $request->input('make_id'),
            'model_id' => (int) $request->input('model_id'),
            'type_id' => (int) $request->input('type_id'),
            'fuel_type_id' => (int) $request->input('fuel_type_id'),
            'transmission_id' => (int) $request->input('transmission_id'),
            'year' => (int) $request->input('year'),
            'color' => $request->input('color'),
            'is_available' => $request->input('is_available') === '1',
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

        return redirect()->route('owner.vehicles.show', $vehicle)->with('success', 'Vehicle updated successfully.');
    }

    public function destroy(Vehicle $vehicle)
    {
        if ($vehicle->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $vehicle->delete();

        return redirect()->route('owner.vehicles.index')->with('success', 'Vehicle deleted.');
    }

    public function uploadPhotos(Request $request, Vehicle $vehicle)
    {
        if ($vehicle->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'photos' => 'required|array',
            'photos.*' => 'required|image|max:5120', // 5MB instead of 2MB
        ], [
            'photos.*.max' => 'Each photo must not be greater than 5MB.',
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
        $query = Vehicle::with(['make', 'model', 'type', 'fuelType', 'pricingTiers', 'transmission', 'owner'])
            ->where('is_available', true);
            // Removed: ->where('status', 'approved');

        // Always exclude vehicles that are blocked today (regardless of date filters)
        $today = Carbon::now()->toDateString();
        $query->whereNotExists(function ($blockQuery) use ($today) {
            $blockQuery->select(DB::raw(1))
                      ->from('vehicle_availability_blocks')
                      ->whereColumn('vehicle_availability_blocks.vehicle_id', 'vehicles.id')
                      ->where(function ($q) use ($today) {
                          // Check if today is directly blocked
                          $q->where('blocked_date', $today)
                          // Check if today falls under a recurring block
                            ->orWhere(function ($recurringQ) use ($today) {
                                $recurringQ->where('is_recurring', true)
                                          ->where('blocked_date', '<=', $today)
                                          ->where(function ($endQ) use ($today) {
                                              $endQ->whereNull('recurring_end_date')
                                                   ->orWhere('recurring_end_date', '>=', $today);
                                          });
                            });
                      });
        });

        // Include rating statistics
        $query->withCount('ratings')
              ->withAvg('ratings', 'rating');

        // Include save count for all vehicles
        $query->withCount('saves');
        
        // For authenticated users, include their save status
        if (Auth::check()) {
            $userId = Auth::id();
            $query->with(['saves' => function ($q) use ($userId) {
                $q->where('user_id', $userId)->where('list_name', 'My Saved Vehicles');
            }]);
        }

        // Text search across make, model, color, and location
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('make', function ($mq) use ($searchTerm) {
                    $mq->where('name', 'like', "%{$searchTerm}%");
                })->orWhereHas('model', function ($mq) use ($searchTerm) {
                    $mq->where('name', 'like', "%{$searchTerm}%");
                })->orWhere('color', 'like', "%{$searchTerm}%")
                  ->orWhere('location_name', 'like', "%{$searchTerm}%")
                  ->orWhere('license_plate', 'like', "%{$searchTerm}%");
            });
        }

        // Vehicle category filter (car or motorcycle)
        if ($request->filled('category')) {
            $query->whereHas('type', function ($q) use ($request) {
                $q->where('category', $request->category);
            });
        }

        // Make filter
        if ($request->filled('make_id')) {
            $query->where('make_id', $request->make_id);
        }

        // Model filter (dependent on make)
        if ($request->filled('model_id')) {
            $query->where('model_id', $request->model_id);
        }

        // Vehicle sub-type filter
        if ($request->filled('type_id')) {
            $query->where('type_id', $request->type_id);
        }

        // Fuel type filter
        if ($request->filled('fuel_type_id')) {
            $query->where('fuel_type_id', $request->fuel_type_id);
        }

        // Transmission filter
        if ($request->filled('transmission_id')) {
            $query->where('transmission_id', $request->transmission_id);
        }

        // Year range filter (removed to simplify interface)
        // if ($request->filled('year_from')) {
        //     $query->where('year', '>=', $request->year_from);
        // }
        // if ($request->filled('year_to')) {
        //     $query->where('year', '<=', $request->year_to);
        // }

        // Color filter (fuzzy search)
        if ($request->filled('color')) {
            $query->where('color', 'like', '%' . $request->color . '%');
        }

        // Price range filter (based on pricing tiers)
        if ($request->filled('price_from') || $request->filled('price_to')) {
            $query->whereHas('pricingTiers', function ($q) use ($request) {
                if ($request->filled('price_from')) {
                    $q->where('price', '>=', $request->price_from);
                }
                if ($request->filled('price_to')) {
                    $q->where('price', '<=', $request->price_to);
                }
            });
        }

        // Availability date filter
        if ($request->filled('available_from') && $request->filled('available_to')) {
            $availableFrom = $request->available_from;
            $availableTo = $request->available_to;
            
            // Exclude vehicles with confirmed bookings overlapping the requested dates
            $query->whereDoesntHave('bookings', function ($q) use ($availableFrom, $availableTo) {
                $q->where('status', 'confirmed')
                  ->where(function ($dateQ) use ($availableFrom, $availableTo) {
                      // Check if pickup is within requested dates
                      $dateQ->whereBetween('pickup_datetime', [$availableFrom, $availableTo])
                      // Note: We'd need to add calculated end datetime logic here
                      // For now, let's use a simpler approach with estimated duration
                            ->orWhere(function ($overlapQ) use ($availableFrom, $availableTo) {
                                $overlapQ->where('pickup_datetime', '<=', $availableFrom)
                                         ->whereRaw('DATE_ADD(pickup_datetime, INTERVAL 1 DAY) >= ?', [$availableTo]);
                            });
                  });
            });

            // Exclude vehicles with availability blocks overlapping the requested dates
            $query->whereNotExists(function ($blockQuery) use ($availableFrom, $availableTo) {
                $fromDate = Carbon::parse($availableFrom)->toDateString();
                $toDate = Carbon::parse($availableTo)->toDateString();
                
                $blockQuery->select(DB::raw(1))
                          ->from('vehicle_availability_blocks')
                          ->whereColumn('vehicle_availability_blocks.vehicle_id', 'vehicles.id')
                          ->where(function ($q) use ($fromDate, $toDate) {
                              // Check direct date blocks that overlap with the requested period
                              $q->whereBetween('blocked_date', [$fromDate, $toDate])
                              // Check recurring blocks that might affect this period
                                ->orWhere(function ($recurringQ) use ($fromDate, $toDate) {
                                    $recurringQ->where('is_recurring', true)
                                              ->where('blocked_date', '<=', $toDate)
                                              ->where(function ($endQ) use ($toDate) {
                                                  $endQ->whereNull('recurring_end_date')
                                                       ->orWhere('recurring_end_date', '>=', $toDate);
                                              });
                                });
                          });
            });
        }

        // Location-based filter (removed to simplify interface)
        // if ($request->filled('location_search') && $request->filled('lat') && $request->filled('lng')) {
        //     $lat = $request->lat;
        //     $lng = $request->lng;
        //     $radius = $request->filled('radius') ? $request->radius : 10; // Default 10km

        //     $query->selectRaw("
        //         vehicles.*, 
        //         (6371 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(lng) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) AS distance
        //     ", [$lat, $lng, $lat])
        //     ->having('distance', '<=', $radius)
        //     ->orderBy('distance');
        // }

        // Features filter (removed to simplify interface)
        // if ($request->filled('features')) {
        //     $features = explode(',', $request->features);
        //     $query->where(function ($q) use ($features) {
        //         foreach ($features as $feature) {
        //             $feature = trim($feature);
        //             $q->orWhere('description', 'like', "%{$feature}%");
        //         }
        //     });
        // }

        // Instant book filter (removed to simplify interface)
        // if ($request->filled('instant_book') && $request->instant_book === 'true') {
        //     $query->where('instant_book_available', true);
        // }

        // Sort options
        $sortBy = $request->get('sort_by', 'latest');
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy(
                    DB::table('vehicle_pricing_tiers')
                        ->select(DB::raw('MIN(price)'))
                        ->join('vehicle_vehicle_pricing_tier', 'vehicle_pricing_tiers.id', '=', 'vehicle_vehicle_pricing_tier.vehicle_pricing_tier_id')
                        ->whereColumn('vehicle_vehicle_pricing_tier.vehicle_id', 'vehicles.id')
                        ->limit(1),
                    'asc'
                );
                break;
            case 'price_high':
                $query->orderBy(
                    DB::table('vehicle_pricing_tiers')
                        ->select(DB::raw('MIN(price)'))
                        ->join('vehicle_vehicle_pricing_tier', 'vehicle_pricing_tiers.id', '=', 'vehicle_vehicle_pricing_tier.vehicle_pricing_tier_id')
                        ->whereColumn('vehicle_vehicle_pricing_tier.vehicle_id', 'vehicles.id')
                        ->limit(1),
                    'desc'
                );
                break;
            case 'year_new':
                $query->orderBy('year', 'desc');
                break;
            case 'year_old':
                $query->orderBy('year', 'asc');
                break;
            case 'rating':
                // Order by average rating (with rating count as tiebreaker)
                $query->orderByDesc('ratings_avg_rating')
                      ->orderByDesc('ratings_count');
                break;
            case 'distance':
                // Already handled in location filter above
                break;
            case 'popular':
                // Order by number of completed bookings
                $query->withCount(['bookings' => function ($q) {
                    $q->where('status', 'completed');
                }])->orderBy('bookings_count', 'desc');
                break;
            default:
                $query->latest();
        }

        $perPage = $request->get('per_page', 9);
        $vehicles = $query->paginate($perPage)->withQueryString();

        // Debug: Log first vehicle to check owner data
        if ($vehicles->count() > 0) {
            $firstVehicle = $vehicles->first();
            Log::info('Vehicle Debug Data:', [
                'vehicle_id' => $firstVehicle->id,
                'owner_id' => $firstVehicle->owner_id,
                'owner_loaded' => $firstVehicle->relationLoaded('owner'),
                'owner_exists' => $firstVehicle->owner ? 'YES' : 'NO',
                'owner_name' => $firstVehicle->owner?->name ?? 'NULL',
                'owner_first_name' => $firstVehicle->owner?->first_name ?? 'NULL',
                'owner_last_name' => $firstVehicle->owner?->last_name ?? 'NULL',
                'raw_owner_data' => $firstVehicle->owner?->toArray() ?? 'NULL'
            ]);
        }

        // Check booking status for each vehicle
        foreach ($vehicles as $vehicle) {
            $activeBooking = Booking::where('vehicle_id', $vehicle->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->where('pickup_datetime', '<=', now()->addDays(7)) // Check bookings within next 7 days
                ->first();
            $vehicle->is_booked = $activeBooking ? true : false;
        }

        // Get popular search terms
        $popularSearches = [
            'Honda Civic', 'Toyota Vios', 'Yamaha', 'Honda Beat', 'Automatic', 'SUV', 'Motorcycle'
        ];

        // Get featured vehicles (highly rated or recently added)
        $featuredVehicles = Vehicle::with(['make', 'model', 'type', 'pricingTiers', 'owner'])
            ->where('is_available', true)
            // Removed: ->where('status', 'approved')
            ->withCount('ratings')
            ->withAvg('ratings', 'rating')
            ->latest()
            ->take(3)
            ->get();

        // Get filter options for the frontend
        $filterOptions = [
            'makes' => VehicleMake::with('models')->orderBy('name')->get(),
            'types' => VehicleType::select('id', 'category', 'sub_type')->orderBy('category')->orderBy('sub_type')->get(),
            'fuelTypes' => FuelType::orderBy('name')->get(),
            'transmissions' => Transmission::orderBy('name')->get(),
            'categories' => VehicleType::select('category')->distinct()->orderBy('category')->pluck('category'),
            'priceRange' => [
                'min' => VehiclePricingTier::min('price') ?: 0,
                'max' => VehiclePricingTier::max('price') ?: 10000
            ],
            'popularColors' => Vehicle::select('color')
                ->groupBy('color')
                ->whereNotNull('color')
                ->orderByRaw('COUNT(*) DESC')
                ->limit(8)
                ->pluck('color')
                ->filter()
                ->values()
                ->toArray(),
            'popularSearches' => $popularSearches,
            'locations' => Vehicle::select('location_name')
                ->groupBy('location_name')
                ->whereNotNull('location_name')
                ->orderByRaw('COUNT(*) DESC')
                ->limit(10)
                ->pluck('location_name')
                ->filter()
                ->values()
                ->toArray()
        ];

        // Determine which page to render based on authentication
        $pageName = Auth::check() ? 'Public/VehiclesRefactored' : 'Public/VehiclesPublic';

        return Inertia::render($pageName, [
            'vehicles' => $vehicles,
            'filterOptions' => $filterOptions,
            'featuredVehicles' => $featuredVehicles,
            'filters' => $request->only([
                'search', 'category', 'make_id', 'model_id', 'type_id', 'fuel_type_id', 'transmission_id',
                'color', 'price_from', 'price_to', 'available_from', 'available_to',
                'sort_by', 'per_page'
            ]),
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
        // Redirect non-authenticated users to login
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('intended', route('public.vehicles.show', $vehicle))
                ->with('message', 'Please log in to view vehicle details and make bookings.');
        }

        $vehicle->load(['make', 'model', 'type', 'fuelType', 'photos', 'owner.vehicles', 'pricingTiers', 'transmission', 'ratings.user']);

        // Check if vehicle has any pending/confirmed bookings
        $activeBooking = \App\Models\Booking::where('vehicle_id', $vehicle->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->first();
        $vehicle->is_booked = $activeBooking ? true : false;

        // Check if current user has any active bookings for this vehicle
        $userActiveBookings = \App\Models\Booking::getUserActiveBookingsForVehicle(Auth::id(), $vehicle->id);

        // Get rating statistics
        $ratingStats = [
            'average_rating' => (float) ($vehicle->average_rating ?? 0),
            'total_ratings' => (int) ($vehicle->total_ratings ?? 0),
            'rating_distribution' => $vehicle->rating_distribution ?? [],
            'recent_ratings' => $vehicle->recent_ratings ?? [],
        ];

        return \Inertia\Inertia::render('Public/VehicleDetail', [
            'vehicle' => $vehicle,
            'userActiveBookings' => $userActiveBookings,
            'ratingStats' => $ratingStats,
        ]);
    }

    // API endpoint for loading models by make
    public function getModelsByMake($makeId)
    {
        try {
            $models = VehicleModel::where('make_id', $makeId)->orderBy('name')->get();
            
            return response()->json([
                'models' => $models,
                'success' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'models' => [],
                'success' => false,
                'message' => 'Failed to load models'
            ], 500);
        }
    }

    /**
     * Show public owner vehicles page
     */
    public function ownerVehiclesPublic($ownerId)
    {
        // Redirect non-authenticated users to login
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('intended', route('owner.vehicles.public', $ownerId))
                ->with('message', 'Please log in to view owner vehicles.');
        }

        // Get the owner
        $owner = \App\Models\User::findOrFail($ownerId);

        // Get owner's vehicles with relationships
        $vehicles = Vehicle::with(['make', 'model', 'type', 'fuelType', 'pricingTiers', 'transmission'])
            ->where('owner_id', $ownerId)
            ->where('is_available', true)
            // Removed: ->where('status', 'approved')
            ->withCount('ratings')
            ->withAvg('ratings', 'rating')
            ->withCount('saves')
            ->get();

        // Check booking status for each vehicle
        foreach ($vehicles as $vehicle) {
            $activeBooking = \App\Models\Booking::where('vehicle_id', $vehicle->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->where('pickup_datetime', '<=', now()->addDays(7))
                ->first();
            $vehicle->is_booked = $activeBooking ? true : false;

            // Check if current user has saved this vehicle
            if (Auth::check()) {
                $vehicle->is_saved = \App\Models\VehicleSave::where('user_id', Auth::id())
                    ->where('vehicle_id', $vehicle->id)
                    ->where('list_name', 'My Saved Vehicles')
                    ->exists();
            } else {
                $vehicle->is_saved = false;
            }
        }

        // Get owner statistics
        $stats = [
            'total_bookings' => \App\Models\Booking::whereIn('vehicle_id', $vehicles->pluck('id'))->count(),
            'followers' => 0, // TODO: Implement followers system
            'average_rating' => $vehicles->avg('ratings_avg_rating') ?: 0,
            'response_rate' => 100, // TODO: Implement response rate calculation
        ];

        return \Inertia\Inertia::render('Public/OwnerVehicles', [
            'owner' => $owner,
            'vehicles' => $vehicles,
            'stats' => $stats,
        ]);
    }
}
