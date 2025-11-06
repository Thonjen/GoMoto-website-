<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        // If user is authenticated, use the normal flow
        if ($request->user()) {
            if ($request->user()->hasVerifiedEmail()) {
                return redirect()->intended(route('dashboard', absolute: false));
            }

            // Log attempt to send verification for authenticated user
            Log::info('Attempting to send email verification (authenticated user)', [
                'user_id' => $request->user()->id,
                'email' => $request->user()->email,
            ]);

            $request->user()->sendEmailVerificationNotification();

            // Log that notification was dispatched
            Log::info('Email verification notification dispatched for authenticated user', [
                'user_id' => $request->user()->id,
                'email' => $request->user()->email,
            ]);

            return back()->with('status', 'verification-link-sent');
        }

        // For non-authenticated users sending verification emails
        $request->validate([
            'email' => 'required|email'
        ]);

        // Security: We should not reveal whether an email exists in our database
        // Instead, we'll always return a success message regardless of whether
        // the email exists or is already verified. This prevents user enumeration.
        
        $user = User::where('email', $request->email)->first();

        // Log attempt to send verification for guest-supplied email
        Log::info('Guest requested email verification', [
            'requested_email' => $request->email,
            'user_found' => $user ? true : false,
            'user_id' => $user ? $user->id : null,
        ]);

        // Only send email if user exists and is not verified
        if ($user && !$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();

            // Log that notification was dispatched for existing user
            Log::info('Email verification notification dispatched for guest-requested email', [
                'requested_email' => $request->email,
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        } else {
            // Log that no notification was sent (either user missing or already verified)
            Log::info('No email verification notification sent for guest-requested email', [
                'requested_email' => $request->email,
                'user_found' => $user ? true : false,
                'user_id' => $user ? $user->id : null,
                'already_verified' => $user ? $user->hasVerifiedEmail() : null,
            ]);
        }

        // Always return success to prevent information disclosure
        return back()->with('status', 'verification-link-sent');
    }
}
