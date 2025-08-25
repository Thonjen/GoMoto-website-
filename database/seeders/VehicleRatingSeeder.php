<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\VehicleRating;
use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;

class VehicleRatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some completed bookings to create ratings for
        $completedBookings = Booking::where('status', 'completed')
                                   ->whereNotNull('actual_return_time')
                                   ->with(['user', 'vehicle'])
                                   ->limit(10)
                                   ->get();

        if ($completedBookings->isEmpty()) {
            $this->command->info('No completed bookings found. Creating some sample data...');
            
            // Get some users and vehicles to create sample bookings
            $users = User::where('role_id', '!=', 1)->limit(5)->get(); // Non-admin users
            $vehicles = Vehicle::with('pricingTiers')->limit(3)->get();
            
            if ($users->isNotEmpty() && $vehicles->isNotEmpty()) {
                foreach ($users as $user) {
                    foreach ($vehicles->take(2) as $vehicle) { // Each user books 2 vehicles
                        $pricingTier = $vehicle->pricingTiers->first();
                        if ($pricingTier) {
                            $booking = Booking::create([
                                'user_id' => $user->id,
                                'vehicle_id' => $vehicle->id,
                                'pricing_tier_id' => $pricingTier->id,
                                'pickup_datetime' => now()->subDays(rand(7, 30)),
                                'status' => 'completed',
                                'total_amount' => $pricingTier->price,
                                'actual_return_time' => now()->subDays(rand(1, 7)),
                                'pickup_location_name' => 'Sample Location',
                                'return_location_name' => 'Sample Return Location',
                            ]);
                            
                            $completedBookings->push($booking);
                        }
                    }
                }
            }
        }

        $sampleComments = [
            "Excellent vehicle! Very clean and well-maintained. The owner was punctual and professional.",
            "Great experience overall. The car was in perfect condition and the owner was very helpful.",
            "Good value for money. Vehicle performed well throughout the rental period.",
            "Amazing ride! The motorcycle was powerful and fuel-efficient. Highly recommended!",
            "Owner was very accommodating and the vehicle exceeded my expectations.",
            "Clean, comfortable, and reliable. Would definitely rent again.",
            "Perfect for my weekend trip. No issues at all during the rental.",
            "The vehicle was exactly as described. Great communication from the owner.",
            "Smooth transaction and great vehicle condition. Very satisfied with the service.",
            "Outstanding experience! The owner went above and beyond to ensure everything was perfect."
        ];

        $ratingCategories = [
            [
                'cleanliness' => 5,
                'condition' => 5,
                'punctuality' => 5,
                'communication' => 5
            ],
            [
                'cleanliness' => 4,
                'condition' => 5,
                'punctuality' => 4,
                'communication' => 5
            ],
            [
                'cleanliness' => 5,
                'condition' => 4,
                'punctuality' => 5,
                'communication' => 4
            ],
            [
                'cleanliness' => 3,
                'condition' => 4,
                'punctuality' => 4,
                'communication' => 4
            ]
        ];

        foreach ($completedBookings->take(15) as $index => $booking) {
            // Skip if rating already exists
            if (VehicleRating::where('booking_id', $booking->id)->exists()) {
                continue;
            }

            $rating = rand(3, 5); // Mostly positive ratings
            $comment = $sampleComments[array_rand($sampleComments)];
            $categories = $ratingCategories[array_rand($ratingCategories)];
            
            VehicleRating::create([
                'user_id' => $booking->user_id,
                'vehicle_id' => $booking->vehicle_id,
                'booking_id' => $booking->id,
                'rating' => $rating,
                'comment' => $comment,
                'would_recommend' => $rating >= 4,
                'rating_categories' => $categories,
                'is_featured' => $rating >= 4 && rand(1, 3) === 1, // 33% chance for good ratings
                'rated_at' => $booking->actual_return_time->addHours(rand(2, 48)), // Rated 2-48 hours after return
            ]);
        }

        $this->command->info('Vehicle ratings seeded successfully!');
    }
}
