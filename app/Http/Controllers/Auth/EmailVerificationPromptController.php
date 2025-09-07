<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|Response
    {
        // Get email from session (after registration) or query parameter
        $email = session('email') ?: $request->query('email');
        
        // Validate email format if provided as query parameter to prevent malicious input
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Invalid email format, don't use it
            $email = null;
        }
        
        // If user is logged in and email is verified, redirect to dashboard
        if ($request->user() && $request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }
        
        // For non-authenticated users, we should NOT check email verification status
        // as this would allow user enumeration attacks and information disclosure.
        // Instead, we simply show the verification page without revealing any data.
        
        // If user is not logged in, show the verification notice
        if (!$request->user()) {
            return Inertia::render('Auth/VerifyEmail', [
                'status' => session('status'),
                'email' => $email
            ]);
        }

        // User is logged in but email not verified, show verification page
        return Inertia::render('Auth/VerifyEmail', [
            'status' => session('status'),
            'email' => $request->user()->email
        ]);
    }
}
