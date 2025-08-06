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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json([
        'user' => $request->user()->load('role'), // ✅ load role relation
    ]);
});

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
    Route::get('/bookings', [VehicleController::class, 'bookingRequests'])->name('owner.bookings.requests');
    Route::get('/bookings/{booking}', [VehicleController::class, 'showBooking'])->name('owner.bookings.show');
    Route::post('/bookings/{booking}/confirm', [VehicleController::class, 'confirmBooking'])->name('owner.bookings.confirm');
    Route::post('/bookings/{booking}/reject', [VehicleController::class, 'rejectBooking'])->name('owner.bookings.reject');
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


Route::get('/ConfirmBooking', function () {
    return Inertia::render('Owner/ConfirmBooking');
})->name('ConfirmBooking');

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
