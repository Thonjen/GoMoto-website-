<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehicleController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json([
        'user' => $request->user()->load('role'), // ✅ load role relation
    ]);
});

// API route for loading models by make
Route::get('/makes/{makeId}/models', [VehicleController::class, 'getModelsByMake']);