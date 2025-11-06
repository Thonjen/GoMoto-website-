<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VerifyEmailController extends Controller
{
    /**
     * Mark the user's email address as verified.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        // Get user ID and hash from route parameters
        $userId = $request->route('id');
        $hash = $request->route('hash');
        Log::info('Incoming email verification attempt', [
            'route_id' => $userId,
            'route_hash' => $hash,
            'ip' => $request->ip(),
            'url' => $request->fullUrl(),
        ]);
        
        // Find the user
        $user = User::findOrFail($userId);
        
        // Verify the hash matches the user's email
        if (!hash_equals(sha1($user->getEmailForVerification()), (string) $hash)) {
            Log::warning('Email verification failed: invalid hash', [
                'user_id' => $user->id,
                'expected_hash' => sha1($user->getEmailForVerification()),
                'provided_hash' => $hash,
                'url' => $request->fullUrl(),
            ]);

            return redirect()->route('login')->withErrors(['email' => 'Invalid verification link.']);
        }
        
        // Check if link is still valid (not expired)
        if (!$request->hasValidSignature()) {
            Log::warning('Email verification failed: invalid or expired signature', [
                'user_id' => $user->id,
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
            ]);

            return redirect()->route('login')->withErrors(['email' => 'Verification link has expired.']);
        }
        
        // If already verified, redirect to login
        if ($user->hasVerifiedEmail()) {
            Log::info('Email verification attempted but already verified', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return redirect()->route('login')->with('status', 'email-already-verified');
        }
        
        // Mark email as verified
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
            Log::info('Email marked as verified', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        }
        
        // Log the user in and redirect to dashboard
        Auth::login($user);
        Log::info('User logged in after verification', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);
        
        return redirect()->route('dashboard')->with('verified', true);
    }
}
