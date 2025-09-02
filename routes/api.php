<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehicleController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json([
        'user' => $request->user()->load('role'), // ✅ load role relation
    ]);
});

// PWA Health check endpoint
Route::get('/health-check', function () {
    return response()->json(['status' => 'ok'], 200);
});

// API route for loading models by make
Route::get('/makes/{makeId}/models', [VehicleController::class, 'getModelsByMake']);