<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\User;

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

            $request->user()->sendEmailVerificationNotification();

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

        // Only send email if user exists and is not verified
        if ($user && !$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
        }

        // Always return success to prevent information disclosure
        return back()->with('status', 'verification-link-sent');
    }
}
