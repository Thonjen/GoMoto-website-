<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PreventDoubleBooking
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only apply to booking store requests
        if ($request->route()->getName() !== 'bookings.store') {
            return $next($request);
        }

        $user = Auth::user();
        $vehicleId = $request->route('vehicle')->id;
        $cacheKey = "booking_attempt_{$user->id}_{$vehicleId}";

        // Check if user has made a booking request for this vehicle in the last 10 seconds
        if (Cache::has($cacheKey)) {
            return back()->withErrors([
                'rate_limit' => 'Please wait before making another booking request for this vehicle.'
            ]);
        }

        // Set cache for 10 seconds to prevent rapid requests
        Cache::put($cacheKey, true, 10);

        $response = $next($request);

        // If booking failed, remove the cache lock immediately
        if ($response->getStatusCode() !== 302 || session()->has('errors')) {
            Cache::forget($cacheKey);
        }

        return $response;
    }
}
