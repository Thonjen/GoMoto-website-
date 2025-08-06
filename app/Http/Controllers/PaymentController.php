<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\VehiclePricingTier;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    // Step 2: Show pricing tiers for a booking
    public function showPricing(Booking $booking)
    {
        $booking->load('vehicle.pricingTiers');
        return Inertia::render('Booking/SelectPricingTier', [
            'booking' => $booking,
            'pricingTiers' => $booking->vehicle->pricingTiers,
        ]);
    }

    // Step 3: User selects a pricing tier
    public function selectTier(Request $request, Booking $booking)
    {
        $request->validate([
            'pricing_tier_id' => 'required|exists:vehicle_pricing_tiers,id',
        ]);
        $tier = VehiclePricingTier::findOrFail($request->pricing_tier_id);
        $booking->update([
            'total_amount' => $tier->price,
        ]);
        // Optionally, store the selected tier id if needed
        // $booking->pricing_tier_id = $tier->id; $booking->save();

        return redirect()->route('booking.payment', $booking->id);
    }

    // Step 4: Show payment page (GCash QR, upload proof)
    public function payment(Booking $booking)
    {
        $booking->load('vehicle.owner');
        $owner = $booking->vehicle->owner;
        return Inertia::render('Booking/Payment', [
            'booking' => $booking,
            'gcashQrUrl' => $owner->gcash_qr ?? null,
        ]);
    }

    // Handle payment proof upload
    public function uploadProof(Request $request, Booking $booking)
    {
        $request->validate([
            'receipt_image' => 'required|image|max:4096',
        ]);
        $path = $request->file('receipt_image')->store('payments', 'public');
        Payment::create([
            'booking_id' => $booking->id,
            'payment_mode_id' => null, // or set if you have modes
            'type' => 'gcash',
            'amount' => $booking->total_amount,
            'reference_number' => null,
            'receipt_image' => '/storage/' . $path,
            'paid_at' => now(),
        ]);
        $booking->update(['status' => 'payment_pending_review']);
        return redirect()->route('booking.payment', $booking->id)->with('success', 'Payment proof uploaded. Awaiting review.');
    }
}
