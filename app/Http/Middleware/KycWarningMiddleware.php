<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class KycWarningMiddleware
{
    /**
     * Handle an incoming request to show warnings when users lack required permissions
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return $next($request);
        }
        
        // Admin users don't need warnings
        if ($user->role && $user->role->name === 'admin') {
            return $next($request);
        }
        
        // Skip warning on profile edit page (where they can fix issues)
        if ($request->routeIs('profile.edit')) {
            return $next($request);
        }
        
        // Check if user is accessing booking-related routes without license
        $isBookingRoute = $request->routeIs([
            'bookings.*',
            'public.vehicles.show',
            'bookings.create',
            'vehicles.saved'
        ]) || $request->is('vehicles/*/book');
        
        // Check if user is accessing owner routes without KYC
        $isOwnerRoute = $request->routeIs('owner.*');
        
        $warningMessage = null;
        $warningType = null;
        
        if ($isBookingRoute) {
            // Check for license requirement
            if (!$user->drivers_license_front || !$user->drivers_license_back) {
                $warningMessage = 'Please upload your driver\'s license to make bookings and access all features.';
                $warningType = 'license_required';
            } elseif ($user->kyc_status !== 'approved') {
                $warningMessage = match($user->kyc_status) {
                    'pending' => 'Your KYC verification is pending. Complete your profile to unlock all features.',
                    'under_review' => 'Your documents are being reviewed. You\'ll be notified once approved.',
                    'rejected' => 'Your KYC verification was rejected. Please update your documents.',
                    default => 'KYC verification is required to access this feature.'
                };
                $warningType = 'kyc_required';
            }
        } elseif ($isOwnerRoute) {
            if ($user->kyc_status !== 'approved') {
                $warningMessage = match($user->kyc_status) {
                    'pending' => 'Complete your KYC verification to list vehicles.',
                    'under_review' => 'Your KYC documents are being reviewed for vehicle listing approval.',
                    'rejected' => 'Your KYC verification was rejected. Please resubmit your documents.',
                    default => 'KYC verification is required to list vehicles.'
                };
                $warningType = 'kyc_required';
            }
        }
        
        // Add warning to session if needed
        if ($warningMessage && $warningType) {
            $request->session()->flash('kyc_warning', [
                'message' => $warningMessage,
                'type' => $warningType,
                'action_url' => route('profile.edit')
            ]);
        }
        
        return $next($request);
    }
}
