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
        
        // If user is not logged in, show the verification notice anyway
        // This handles cases where users are redirected after registration
        if (!$request->user()) {
            return Inertia::render('Auth/VerifyEmail', [
                'status' => session('status'),
                'email' => $email
            ]);
        }

        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(route('dashboard', absolute: false))
                    : Inertia::render('Auth/VerifyEmail', [
                        'status' => session('status'),
                        'email' => $request->user()->email
                    ]);
    }
}
