<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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
        
        // Find the user
        $user = User::findOrFail($userId);
        
        // Verify the hash matches the user's email
        if (!hash_equals(sha1($user->getEmailForVerification()), (string) $hash)) {
            return redirect()->route('login')->withErrors(['email' => 'Invalid verification link.']);
        }
        
        // Check if link is still valid (not expired)
        if (!$request->hasValidSignature()) {
            return redirect()->route('login')->withErrors(['email' => 'Verification link has expired.']);
        }
        
        // If already verified, redirect to login
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')->with('status', 'email-already-verified');
        }
        
        // Mark email as verified
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }
        
        // Log the user in and redirect to dashboard
        Auth::login($user);
        
        return redirect()->route('dashboard')->with('verified', true);
    }
}
