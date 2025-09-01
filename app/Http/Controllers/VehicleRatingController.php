<?php

namespace App\Http\Controllers;

use App\Models\VehicleRating;
use App\Models\Booking;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class VehicleRatingController extends Controller
{
    /**
     * Show rating form for a specific booking
     */
    public function create(Booking $booking)
    {
        // Check if user owns this booking
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Check if booking is eligible for rating
        if (!$booking->isEligibleForRating()) {
            return redirect()->route('dashboard')->with('error', 'This booking is not eligible for rating.');
        }

        $booking->load(['vehicle.make', 'vehicle.model', 'vehicle.type', 'vehicle.owner', 'pricingTier']);

        return Inertia::render('Rating/Create', [
            'booking' => $booking,
            'vehicle' => $booking->vehicle,
        ]);
    }

    /**
     * Store a new rating
     */
    public function store(Request $request, Booking $booking)
    {
        // Check if user owns this booking
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Check if booking is eligible for rating
        if (!$booking->isEligibleForRating()) {
            return back()->withErrors(['error' => 'This booking is not eligible for rating.']);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'would_recommend' => 'boolean',
            'rating_categories' => 'nullable|array',
            'rating_categories.cleanliness' => 'nullable|integer|min:1|max:5',
            'rating_categories.condition' => 'nullable|integer|min:1|max:5',
            'rating_categories.punctuality' => 'nullable|integer|min:1|max:5',
            'rating_categories.communication' => 'nullable|integer|min:1|max:5',
        ]);

        $rating = VehicleRating::create([
            'user_id' => Auth::id(),
            'vehicle_id' => $booking->vehicle_id,
            'booking_id' => $booking->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'would_recommend' => $request->boolean('would_recommend', true),
            'rating_categories' => $request->rating_categories,
            'is_featured' => $request->rating >= 4 && !empty($request->comment), // Auto-feature good reviews with comments
            'rated_at' => now(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Thank you for your rating! Your feedback helps other renters.');
    }

    /**
     * Get ratings for a specific vehicle (for public display)
     */
    public function vehicleRatings(Vehicle $vehicle)
    {
        $ratings = $vehicle->ratings()
                          ->with(['user'])
                          ->orderBy('is_featured', 'desc')
                          ->orderBy('rated_at', 'desc')
                          ->paginate(10);

        return response()->json([
            'ratings' => $ratings,
            'average_rating' => $vehicle->average_rating,
            'total_ratings' => $vehicle->total_ratings,
            'rating_distribution' => $vehicle->rating_distribution,
        ]);
    }

    /**
     * Show all ratings for owner's vehicles
     */
    public function ownerIndex()
    {
        $owner = Auth::user();
        
        $ratings = VehicleRating::whereHas('vehicle', function($query) use ($owner) {
                $query->where('owner_id', $owner->id);
            })
            ->with(['user', 'vehicle.make', 'vehicle.model', 'booking'])
            ->orderBy('rated_at', 'desc')
            ->paginate(20);

        // Get rating statistics for owner's vehicles
        $totalRatings = VehicleRating::whereHas('vehicle', function($query) use ($owner) {
                $query->where('owner_id', $owner->id);
            })->count();

        $averageRating = VehicleRating::whereHas('vehicle', function($query) use ($owner) {
                $query->where('owner_id', $owner->id);
            })->avg('rating') ?? 0;

        $recentRatings = VehicleRating::whereHas('vehicle', function($query) use ($owner) {
                $query->where('owner_id', $owner->id);
            })
            ->where('rated_at', '>=', now()->subDays(30))
            ->count();

        return Inertia::render('Owner/Ratings/Index', [
            'ratings' => $ratings,
            'statistics' => [
                'total_ratings' => $totalRatings,
                'average_rating' => round($averageRating, 1),
                'recent_ratings' => $recentRatings,
            ]
        ]);
    }

    /**
     * Get bookings eligible for rating for current user
     */
    public function eligibleBookings()
    {
        $bookings = Booking::where('user_id', Auth::id())
                          ->whereDoesntHave('rating')
                          ->where('status', 'completed')
                          ->whereNotNull('return_time')
                          ->where('return_time', '>=', now()->subDays(7)) // Within last 7 days
                          ->with(['vehicle.make', 'vehicle.model', 'vehicle.owner'])
                          ->orderBy('return_time', 'desc')
                          ->get();

        return response()->json([
            'bookings' => $bookings
        ]);
    }

    /**
     * Prompt user to rate after booking completion (AJAX endpoint)
     */
    public function promptRating(Request $request)
    {
        $user = Auth::user();
        
        // Get bookings that should prompt for rating
        $bookingsToRate = Booking::where('user_id', $user->id)
                                ->whereDoesntHave('rating')
                                ->where('status', 'completed')
                                ->whereNotNull('return_time')
                                ->where('return_time', '>=', now()->subHours(2)) // At least 2 hours after completion
                                ->where('return_time', '<=', now()->subDays(7)) // But not older than 7 days
                                ->with(['vehicle.make', 'vehicle.model'])
                                ->limit(1)
                                ->first();

        if ($bookingsToRate) {
            return response()->json([
                'should_prompt' => true,
                'booking' => $bookingsToRate
            ]);
        }

        return response()->json(['should_prompt' => false]);
    }
}
