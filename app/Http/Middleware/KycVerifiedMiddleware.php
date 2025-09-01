<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class KycVerifiedMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $action = 'book'): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Check if user is banned or suspended (should be caught by CheckBannedUser middleware first)
        if (in_array($user->status, ['banned', 'suspended'])) {
            abort(403, 'Your account access has been restricted.');
        }
        
        // Admin users bypass all KYC verification requirements
        if ($user->role && $user->role->name === 'admin') {
            return $next($request);
        }
        
        // Check KYC status and required permissions for non-admin users
        if ($user->kyc_status !== 'approved') {
            $message = match($user->kyc_status) {
                'pending' => 'Please complete your KYC verification by uploading your driver\'s license.',
                'under_review' => 'Your KYC documents are being reviewed. Please wait for approval.',
                'rejected' => 'Your KYC verification was rejected. Please check your profile and resubmit required documents.',
                default => 'KYC verification is required to proceed.'
            };
            
            return redirect()->route('profile.edit')->with('error', $message);
        }
        
        // Check specific action permissions
        if ($action === 'book' && !$user->can_book) {
            return redirect()->route('profile.edit')
                ->with('error', 'You are not authorized to make bookings. Please ensure you have uploaded valid driver\'s license documents.');
        }
        
        if ($action === 'list_vehicles' && !$user->can_list_vehicles) {
            return redirect()->route('profile.edit')
                ->with('error', 'You are not authorized to list vehicles. Please complete your KYC verification.');
        }
        
        // Check if user has uploaded driver's license for booking
        if ($action === 'book' && (!$user->drivers_license_front || !$user->drivers_license_back)) {
            return redirect()->route('profile.edit')
                ->with('error', 'Please upload both front and back photos of your driver\'s license to make bookings.');
        }
        
        return $next($request);
    }
}
