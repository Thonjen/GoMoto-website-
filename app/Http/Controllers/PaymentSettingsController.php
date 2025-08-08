<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class PaymentSettingsController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        
        return Inertia::render('Owner/PaymentSettings', [
            'user' => [
                'accepts_cod' => $user->accepts_cod,
                'accepts_gcash' => $user->accepts_gcash,
                'has_gcash_qr' => $user->gcash_qr !== null,
                'gcash_qr_url' => $user->gcash_qr ? '/storage/' . $user->gcash_qr : null,
            ]
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'accepts_cod' => 'boolean',
            'accepts_gcash' => 'boolean',
        ]);

        // If user tries to enable GCash but has no QR code, prevent it
        if ($request->accepts_gcash && !$user->gcash_qr) {
            return back()->withErrors([
                'accepts_gcash' => 'You must upload a GCash QR code before enabling GCash payments.'
            ]);
        }

        // Ensure at least one payment method is enabled
        if (!$request->accepts_cod && !$request->accepts_gcash) {
            return back()->withErrors([
                'payment_methods' => 'You must accept at least one payment method.'
            ]);
        }

        $user->update([
            'accepts_cod' => $request->accepts_cod ?? false,
            'accepts_gcash' => $request->accepts_gcash ?? false,
        ]);

        return back()->with('success', 'Payment settings updated successfully!');
    }
}
