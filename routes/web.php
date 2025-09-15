<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehiclePlaceController;
use App\Http\Controllers\OwnerGcashQrController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PricingTierController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\VehicleDataController;
use App\Http\Controllers\OverchargeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OwnerDashboardController;
use App\Http\Controllers\PaymentSettingsController;
use App\Http\Controllers\AdminController;
use App\Models\Vehicle;



//API FOR MOBILE

Route::get('/api/public-vehicles', function () {
    $vehicles = Vehicle::with(['make', 'model', 'type', 'fuelType', 'photos', 'pricingTiers', 'transmission', 'owner'])
        ->latest()
        ->take(20)
        ->get();
    return response()->json($vehicles);
});




Route::get('/', function () {
    return Inertia::render('Landing', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('Landing');

// PWA Offline page
Route::get('/offline', function () {
    return Inertia::render('Offline');
})->name('offline');

// PWA Status and Testing page (for development/testing)
Route::get('/pwa-status', function () {
    return Inertia::render('PWAStatus');
})->name('pwa.status');

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'check.banned'])
    ->name('dashboard');

Route::middleware(['auth', 'check.banned'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update.post');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/kyc/submit', [ProfileController::class, 'submitKyc'])->name('profile.kyc.submit');
    Route::delete('/profile/picture', [ProfileController::class, 'deleteProfilePicture'])->name('profile.picture.delete');
});

Route::get('/privacy', function () {
    return Inertia::render('Public/Privacy');
})->name('privacy');

Route::get('/terms', function () {
    return Inertia::render('Public/Terms');
})->name('terms');

Route::get('/search', function () {
    return Inertia::render('Public/Search');
})->name('search');

Route::middleware(['auth', 'check.banned', 'role:owner,admin', 'kyc.verified:list_vehicles'])->prefix('owner')->group(function () {
    // Owner Dashboard
    Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('owner.dashboard');

    Route::post('/bookings/{booking}/calculate-overcharges', [OverchargeController::class, 'calculateOvercharges'])->name('overcharges.calculate');
    Route::post('/bookings/{booking}/add-overcharge', [OverchargeController::class, 'addManualOvercharge'])->name('owner.overcharges.add');

    Route::get('/vehicles', [VehicleController::class, 'index'])->name('owner.vehicles.index');
    Route::get('/vehicles/create', [VehicleController::class, 'create'])->name('owner.vehicles.create');
    Route::post('/vehicles', [VehicleController::class, 'store'])->name('owner.vehicles.store');
    Route::get('/vehicles/{vehicle}', [VehicleController::class, 'show'])->name('owner.vehicles.show');
    Route::get('/vehicles/{vehicle}/edit', [VehicleController::class, 'edit'])->name('owner.vehicles.edit');
    Route::put('/vehicles/{vehicle}', [VehicleController::class, 'update'])->name('owner.vehicles.update');
    Route::delete('/vehicles/{vehicle}', [VehicleController::class, 'destroy'])->name('owner.vehicles.destroy');
    Route::post('/vehicles/{vehicle}/photos', [VehicleController::class, 'uploadPhotos']);
    Route::delete('/vehicles/photos/{photo}', [VehicleController::class, 'deletePhoto'])->name('vehicles.photos.destroy');
    
    // Vehicle Availability Management
    Route::get('/vehicles/{vehicle}/availability', [App\Http\Controllers\VehicleAvailabilityController::class, 'show'])->name('owner.vehicles.availability');
    Route::post('/vehicles/{vehicle}/availability', [App\Http\Controllers\VehicleAvailabilityController::class, 'store'])->name('owner.vehicles.availability.store');
    Route::post('/vehicles/{vehicle}/availability/bulk', [App\Http\Controllers\VehicleAvailabilityController::class, 'bulkStore'])->name('owner.vehicles.availability.bulk');
    Route::post('/vehicles/{vehicle}/availability/recurring-days', [App\Http\Controllers\VehicleAvailabilityController::class, 'storeRecurringDays'])->name('owner.vehicles.availability.recurring-days');
    Route::put('/vehicles/{vehicle}/availability/{block}', [App\Http\Controllers\VehicleAvailabilityController::class, 'update'])->name('owner.vehicles.availability.update');
    Route::delete('/vehicles/{vehicle}/availability/{block}', [App\Http\Controllers\VehicleAvailabilityController::class, 'destroy'])->name('owner.vehicles.availability.destroy');
    Route::get('/api/vehicles/{vehicle}/availability-data', [App\Http\Controllers\VehicleAvailabilityController::class, 'getAvailabilityData'])->name('api.vehicles.availability');
    
    Route::get('/UploadQrCode', [OwnerGcashQrController::class, 'show'])->name('owner.gcash-qr.show');
    Route::post('/UploadQrCode', [OwnerGcashQrController::class, 'store'])->name('owner.gcash-qr.store');
    Route::delete('/UploadQrCode', [OwnerGcashQrController::class, 'destroy'])->name('owner.gcash-qr.destroy');
    
    // Payment Settings Routes
    Route::get('/payment-settings', [PaymentSettingsController::class, 'show'])->name('owner.payment-settings.show');
    Route::post('/payment-settings', [PaymentSettingsController::class, 'update'])->name('owner.payment-settings.update');
    
    // Owner rating management
    Route::get('/ratings', [App\Http\Controllers\VehicleRatingController::class, 'ownerIndex'])->name('owner.ratings.index');
    
    Route::get('/bookings', [BookingController::class, 'ownerIndex'])->name('owner.bookings.index');
    Route::get('/bookings/calendar', [BookingController::class, 'ownerCalendar'])->name('owner.bookings.calendar');
    Route::get('/bookings/{booking}', [BookingController::class, 'ownerShow'])->name('owner.bookings.show');
    Route::post('/bookings/{booking}/confirm', [BookingController::class, 'confirm'])->name('owner.bookings.confirm');
    Route::post('/bookings/{booking}/reject', [BookingController::class, 'reject'])->name('owner.bookings.reject');
    Route::post('/bookings/{booking}/confirm-payment', [BookingController::class, 'confirmPayment'])->name('owner.bookings.confirmPayment');
    Route::post('/bookings/{booking}/complete', [BookingController::class, 'complete'])->name('owner.bookings.complete');
    Route::get('/pricing-tiers', [PricingTierController::class, 'index'])->name('owner.pricing-tiers.index');
    Route::get('/pricing-tiers/list', [PricingTierController::class, 'list'])->name('owner.pricing-tiers.list');
    Route::post('/pricing-tiers', [PricingTierController::class, 'store'])->name('owner.pricing-tiers.store');
    Route::get('/pricing-tiers/{id}/edit', [PricingTierController::class, 'edit'])->name('owner.pricing-tiers.edit');
    Route::put('/pricing-tiers/{id}', [PricingTierController::class, 'update'])->name('owner.pricing-tiers.update');
    Route::post('/pricing-tiers/{id}', [PricingTierController::class, 'update']); // for Inertia forms
    Route::delete('/pricing-tiers/{id}', [PricingTierController::class, 'destroy'])->name('owner.pricing-tiers.destroy');
    
    Route::get('/vehicle-data-stats', [VehicleDataController::class, 'stats'])->name('owner.vehicle-data-stats');
    
    // Overcharge management routes
    Route::post('/overcharges/{overcharge}/mark-paid', [OverchargeController::class, 'markAsPaid'])->name('owner.overcharges.markPaid');
    Route::get('/overcharges', [OverchargeController::class, 'index'])->name('owner.overcharges.settings');
    Route::post('/overcharges/settings', [OverchargeController::class, 'updateSettings'])->name('owner.overcharges.updateSettings');
    Route::get('/overcharges/stats', [OverchargeController::class, 'stats'])->name('owner.overcharges.stats');
   
   
    // Extension request management routes
    Route::get('/extension-requests', [App\Http\Controllers\BookingExtensionController::class, 'ownerIndex'])->name('owner.extensionRequests.index');
    Route::post('/extension-requests/{extensionRequest}/approve', [App\Http\Controllers\BookingExtensionController::class, 'approve'])->name('owner.extensionRequests.approve');
    Route::post('/extension-requests/{extensionRequest}/reject', [App\Http\Controllers\BookingExtensionController::class, 'reject'])->name('owner.extensionRequests.reject');
    Route::post('/vehicles/{vehicle}/extension-settings', [App\Http\Controllers\BookingExtensionController::class, 'updateVehicleSettings'])->name('owner.vehicles.extensionSettings');
});

// Public vehicle listing
Route::get('/vehicles', [VehicleController::class, 'publicIndex'])->name('public.vehicles.index');
Route::get('/vehicles/{vehicle}', [VehicleController::class, 'publicShow'])->name('public.vehicles.show');
Route::get('/owner/{owner}/vehicles', [VehicleController::class, 'ownerVehiclesPublic'])->name('owner.vehicles.public');

// Booking routes (authenticated users)
Route::middleware(['auth', 'check.banned'])->group(function () {
    // Renter booking routes (require KYC verification)
    Route::middleware(['kyc.verified:book'])->group(function () {
        Route::get('/vehicles/{vehicle}/book', [BookingController::class, 'create'])->name('bookings.create');
        Route::post('/vehicles/{vehicle}/book', [BookingController::class, 'store'])
            ->name('bookings.store')
            ->middleware(\App\Http\Middleware\PreventDoubleBooking::class);
        Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
        Route::post('/bookings/{booking}/request-extension', [App\Http\Controllers\BookingExtensionController::class, 'requestExtension'])->name('bookings.requestExtension');
    });
    
    // Booking management routes (require KYC verification for renters)
    Route::middleware(['kyc.verified:view_bookings'])->group(function () {
        Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    });
    
    // Payment routes (no KYC required - already authenticated booking)
    Route::get('/bookings/{booking}/payment', [BookingController::class, 'payment'])->name('bookings.payment');
    Route::post('/bookings/{booking}/upload-receipt', [BookingController::class, 'uploadReceipt'])->name('bookings.uploadReceipt');
    
    // Rating routes
    Route::get('/bookings/{booking}/rate', [App\Http\Controllers\VehicleRatingController::class, 'create'])->name('ratings.create');
    Route::post('/bookings/{booking}/rate', [App\Http\Controllers\VehicleRatingController::class, 'store'])->name('ratings.store');
    Route::get('/api/ratings/eligible-bookings', [App\Http\Controllers\VehicleRatingController::class, 'eligibleBookings'])->name('ratings.eligible');
    
    // Vehicle Save/Wishlist routes (require KYC verification for renters)
    Route::middleware(['kyc.verified:save_vehicles'])->group(function () {
        Route::get('/saved-vehicles', [App\Http\Controllers\VehicleSaveController::class, 'index'])->name('vehicles.saved');
        Route::post('/api/vehicles/save', [App\Http\Controllers\VehicleSaveController::class, 'store'])->name('vehicles.save.store');
        Route::delete('/api/vehicles/save', [App\Http\Controllers\VehicleSaveController::class, 'destroy'])->name('vehicles.save.destroy');
        Route::post('/api/vehicles/save/toggle', [App\Http\Controllers\VehicleSaveController::class, 'toggle'])->name('vehicles.save.toggle');
        Route::get('/api/vehicles/save/check', [App\Http\Controllers\VehicleSaveController::class, 'check'])->name('vehicles.save.check');
        Route::delete('/api/vehicles/save/delete-list', [App\Http\Controllers\VehicleSaveController::class, 'deleteList'])->name('vehicles.save.deleteList');
    });
    Route::get('/api/ratings/prompt', [App\Http\Controllers\VehicleRatingController::class, 'promptRating'])->name('ratings.prompt');
});


// Booking payment flow (public, after booking is created)
Route::middleware(['auth', 'check.banned'])->group(function () {
    Route::get('/booking/{booking}/pricing', [PaymentController::class, 'showPricing'])->name('booking.pricing');
    Route::post('/booking/{booking}/select-tier', [PaymentController::class, 'selectTier'])->name('booking.selectTier');
    Route::get('/booking/{booking}/payment', [PaymentController::class, 'payment'])->name('booking.payment');
    Route::post('/booking/{booking}/payment-proof', [PaymentController::class, 'uploadProof'])->name('booking.paymentProof');
});

// Vehicle data API routes (for dropdowns and NHTSA integration)
Route::prefix('api/vehicle-data')->group(function () {
    Route::get('/vehicle-types', [VehicleDataController::class, 'getVehicleTypes']);
    Route::get('/makes/{vehicleType}', [VehicleDataController::class, 'getMakesByType']);
    Route::get('/models/{makeId}', [VehicleDataController::class, 'getModelsByMake']);
    Route::get('/fuel-types', [VehicleDataController::class, 'getFuelTypes']);
    Route::get('/transmissions', [VehicleDataController::class, 'getTransmissions']);
    Route::get('/years', [VehicleDataController::class, 'getYears']);
    
    // Search endpoints
    Route::get('/search/makes', [VehicleDataController::class, 'searchMakes']);
    Route::get('/search/models', [VehicleDataController::class, 'searchModels']);
    
    // Admin/Owner routes for adding data
    Route::middleware(['auth', 'role:owner,admin'])->group(function () {
        // Philippine vehicle data population (replaces NHTSA API)
        Route::post('/populate/philippine-data', [VehicleDataController::class, 'populatePhilippineData']);
        Route::post('/populate/car-makes', [VehicleDataController::class, 'populateCarMakes']);
        Route::post('/populate/motorcycle-makes', [VehicleDataController::class, 'populateMotorcycleMakes']);
        Route::post('/populate/models/{makeId}', [VehicleDataController::class, 'populateModelsForMake']);
        
        // Manual additions
        Route::post('/makes', [VehicleDataController::class, 'addMake']);
        Route::post('/models', [VehicleDataController::class, 'addModel']);
        Route::post('/fuel-types', [VehicleDataController::class, 'addFuelType']);
        Route::post('/transmissions', [VehicleDataController::class, 'addTransmission']);
    });
});

// Add this route for the Edit.vue component
Route::prefix('api')->group(function () {
    Route::get('/makes/{makeId}/models', [VehicleController::class, 'getModelsByMake']);
});

// Admin/Owner route to populate vehicle data
Route::middleware(['auth', 'role:owner,admin'])->group(function () {
    Route::post('/admin/populate-vehicle-data', function () {
        Artisan::call('vehicle-data:populate-ph', ['--fresh' => true]);
        return back()->with('success', 'Philippine vehicle data populated successfully!');
    })->name('admin.populate-vehicle-data');
});

// Admin routes
Route::middleware(['auth', 'check.banned', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // User Management
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users/{user}/ban', [AdminController::class, 'banUser'])->name('users.ban');
    Route::post('/users/{user}/unban', [AdminController::class, 'unbanUser'])->name('users.unban');
    Route::post('/users/{user}/update-role', [AdminController::class, 'updateUserRole'])->name('users.update-role');
    
    // Vehicle Management
    Route::get('/vehicles', [AdminController::class, 'vehicles'])->name('vehicles');
    Route::post('/vehicles/{vehicle}/approve', [AdminController::class, 'approveVehicle'])->name('vehicles.approve');
    Route::post('/vehicles/{vehicle}/reject', [AdminController::class, 'rejectVehicle'])->name('vehicles.reject');
    Route::post('/vehicles/{vehicle}/suspend', [AdminController::class, 'suspendVehicle'])->name('vehicles.suspend');
    
    // Booking Management
    Route::get('/bookings', [AdminController::class, 'bookings'])->name('bookings');
    Route::post('/bookings/{booking}/cancel', [AdminController::class, 'cancelBooking'])->name('bookings.cancel');
    Route::post('/bookings/{booking}/refund', [AdminController::class, 'refundBooking'])->name('bookings.refund');
    
    // Payment Management
    Route::get('/payments', [AdminController::class, 'payments'])->name('payments');
    
    // Extension Requests
    Route::get('/extension-requests', [AdminController::class, 'extensionRequests'])->name('extension-requests');
    
    // KYC Verification Management
    Route::get('/kyc/verifications', [AdminController::class, 'kycVerifications'])->name('kyc.verifications');
    Route::post('/kyc/{user}/approve', [AdminController::class, 'approveKyc'])->name('kyc.approve');
    Route::post('/kyc/{user}/reject', [AdminController::class, 'rejectKyc'])->name('kyc.reject');
    Route::get('/kyc/logs', [AdminController::class, 'kycLogs'])->name('kyc.logs');
    
    // Overcharge Management
    Route::get('/overcharges', [AdminController::class, 'overcharges'])->name('overcharges');
    
    // Reports
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    
    // Settings
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
});

// Public API routes for ratings
Route::get('/api/vehicles/{vehicle}/ratings', [App\Http\Controllers\VehicleRatingController::class, 'vehicleRatings'])->name('api.vehicles.ratings');

require __DIR__.'/auth.php';
