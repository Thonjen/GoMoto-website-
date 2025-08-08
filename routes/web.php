<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehiclePlaceController;
use App\Http\Controllers\OwnerGcashQrController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PricingTierController;
use App\Http\Controllers\BookingController;


Route::get('/', function () {
    return Inertia::render('Landing', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('Landing');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
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

Route::middleware(['auth', 'role:owner, admin'])->prefix('owner')->group(function () {
    Route::get('/vehicles', [VehicleController::class, 'index'])->name('vehicles.index');
    Route::get('/vehicles/create', [VehicleController::class, 'create'])->name('vehicles.create');
    Route::post('/vehicles', [VehicleController::class, 'store']);
    Route::get('/vehicles/{vehicle}', [VehicleController::class, 'show'])->name('vehicles.show');
    Route::get('/vehicles/{vehicle}/edit', [VehicleController::class, 'edit'])->name('vehicles.edit');
    Route::put('/vehicles/{vehicle}', [VehicleController::class, 'update'])->name('vehicles.update');
    Route::delete('/vehicles/{vehicle}', [VehicleController::class, 'destroy'])->name('vehicles.destroy');
    Route::post('/vehicles/{vehicle}/photos', [VehicleController::class, 'uploadPhotos']);
    Route::delete('/vehicles/photos/{photo}', [VehicleController::class, 'deletePhoto'])->name('vehicles.photos.destroy');
    Route::get('/UploadQrCode', [OwnerGcashQrController::class, 'show'])->name('owner.gcash-qr.show');
    Route::post('/UploadQrCode', [OwnerGcashQrController::class, 'store'])->name('owner.gcash-qr.store');
    Route::delete('/UploadQrCode', [OwnerGcashQrController::class, 'destroy'])->name('owner.gcash-qr.destroy');
    
    // Payment Settings Routes
    Route::get('/payment-settings', [App\Http\Controllers\PaymentSettingsController::class, 'show'])->name('owner.payment-settings.show');
    Route::post('/payment-settings', [App\Http\Controllers\PaymentSettingsController::class, 'update'])->name('owner.payment-settings.update');
    
    Route::get('/bookings', [BookingController::class, 'ownerIndex'])->name('owner.bookings.index');
    Route::get('/bookings/calendar', [BookingController::class, 'ownerCalendar'])->name('owner.bookings.calendar');
    Route::get('/bookings/{booking}', [BookingController::class, 'ownerShow'])->name('owner.bookings.show');
    Route::post('/bookings/{booking}/confirm', [BookingController::class, 'confirm'])->name('owner.bookings.confirm');
    Route::post('/bookings/{booking}/reject', [BookingController::class, 'reject'])->name('owner.bookings.reject');
    Route::post('/bookings/{booking}/confirm-payment', [BookingController::class, 'confirmPayment'])->name('owner.bookings.confirmPayment');
    Route::post('/bookings/{booking}/complete', [BookingController::class, 'complete'])->name('owner.bookings.complete');
    Route::get('/pricing-tiers', [PricingTierController::class, 'index']);
    Route::get('/pricing-tiers/list', [PricingTierController::class, 'list']);
    Route::post('/pricing-tiers', [PricingTierController::class, 'store']);
    Route::get('/pricing-tiers/{id}/edit', [PricingTierController::class, 'edit']);
    Route::put('/pricing-tiers/{id}', [PricingTierController::class, 'update']);
    Route::post('/pricing-tiers/{id}', [PricingTierController::class, 'update']); // for Inertia forms
    Route::delete('/pricing-tiers/{id}', [PricingTierController::class, 'destroy']);
});

// Public vehicle listing
Route::get('/vehicles', [VehicleController::class, 'publicIndex'])->name('public.vehicles.index');
Route::get('/vehicles/{vehicle}', [VehicleController::class, 'publicShow'])->name('public.vehicles.show');

// Booking routes (authenticated users)
Route::middleware(['auth'])->group(function () {
    // Renter booking routes
    Route::get('/vehicles/{vehicle}/book', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/vehicles/{vehicle}/book', [BookingController::class, 'store'])
        ->name('bookings.store')
        ->middleware(\App\Http\Middleware\PreventDoubleBooking::class);
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::get('/bookings/{booking}/payment', [BookingController::class, 'payment'])->name('bookings.payment');
    Route::post('/bookings/{booking}/upload-receipt', [BookingController::class, 'uploadReceipt'])->name('bookings.uploadReceipt');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
});


Route::get('/ConfirmBooking', function () {
    return Inertia::render('Owner/ConfirmBooking');
})->name('booking.confirm');

Route::get('/booking-requests', function () {
    return Inertia::render('Owner/BookingRequests');
})->name('booking.requests');


Route::get('/MyPayments', function () {
    return Inertia::render('Owner/MyPayments');
})->name('MyPayments');

Route::get('/EditVehicle', function () {
    return Inertia::render('Owner/EditVehicle');
})->name('EditVehicle');

Route::get('/MyVehicles', function () {
    return Inertia::render('Owner/MyVehicles');
})->name('MyVehicles');

Route::get('/VehicleDetail', function () {
    return Inertia::render('Public/VehicleDetail');
})->name('VehicleDetail');

Route::get('/Search', function () {
    return Inertia::render('Public/Search');
})->name('Search');

// Booking payment flow (public, after booking is created)
Route::middleware(['auth'])->group(function () {
    Route::get('/booking/{booking}/pricing', [PaymentController::class, 'showPricing'])->name('booking.pricing');
    Route::post('/booking/{booking}/select-tier', [PaymentController::class, 'selectTier'])->name('booking.selectTier');
    Route::get('/booking/{booking}/payment', [PaymentController::class, 'payment'])->name('booking.payment');
    Route::post('/booking/{booking}/payment-proof', [PaymentController::class, 'uploadProof'])->name('booking.paymentProof');
});

require __DIR__.'/auth.php';
