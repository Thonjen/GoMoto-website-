<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehiclePlaceController;
use App\Http\Controllers\OwnerGcashQrController;


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

Route::get('/user', function (Request $request) {
    return ['user' => $request->user()];
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
    Route::get('/gcash-qr', [OwnerGcashQrController::class, 'show'])->name('owner.gcash-qr.show');
    Route::post('/gcash-qr', [OwnerGcashQrController::class, 'store'])->name('owner.gcash-qr.store');
    Route::delete('/gcash-qr', [OwnerGcashQrController::class, 'destroy'])->name('owner.gcash-qr.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('vehicle-places', VehiclePlaceController::class);
});

// Public vehicle listing
Route::get('/vehicles', [VehicleController::class, 'publicIndex'])->name('public.vehicles.index');


Route::get('/AddVehicle', function () {
    return Inertia::render('Owner/AddVehicle');
})->name('addvehicle');

Route::get('/EditVehicle', function () {
    return Inertia::render('Owner/EditVehicle');
})->name('editvehicle');

require __DIR__.'/auth.php';
