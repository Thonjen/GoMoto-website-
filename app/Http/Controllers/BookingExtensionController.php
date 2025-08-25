<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingExtensionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BookingExtensionController extends Controller
{
    /**
     * Request an extension for a booking
     */
    public function requestExtension(Request $request, Booking $booking)
    {
        // Check if user owns this booking
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Validate the booking can be extended
        if (!$booking->canBeExtended()) {
            return back()->withErrors([
                'extension' => 'This booking cannot be extended at this time.'
            ]);
        }

        $request->validate([
            'requested_hours' => 'required|integer|min:1|max:' . $booking->vehicle->max_extension_hours,
            'reason' => 'nullable|string|max:500',
        ]);

        // Check if there's already a pending request
        if ($booking->pendingExtensionRequest) {
            return back()->withErrors([
                'extension' => 'You already have a pending extension request for this booking.'
            ]);
        }

        $calculatedCost = $booking->calculateExtensionCost($request->requested_hours);

        // Create the extension request
        $extensionRequest = BookingExtensionRequest::create([
            'booking_id' => $booking->id,
            'requested_hours' => $request->requested_hours,
            'reason' => $request->reason,
            'calculated_cost' => $calculatedCost,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Extension request submitted successfully! The vehicle owner will review your request.');
    }

    /**
     * Get extension requests for owner
     */
    public function ownerIndex()
    {
        $extensionRequests = BookingExtensionRequest::whereHas('booking.vehicle', function ($query) {
            $query->where('owner_id', Auth::id());
        })
        ->with(['booking.user', 'booking.vehicle.brand', 'booking.vehicle.vehicleType'])
        ->orderBy('created_at', 'desc')
        ->paginate(20);

        return Inertia::render('Owner/ExtensionRequests/Index', [
            'extensionRequests' => $extensionRequests,
        ]);
    }

    /**
     * Approve an extension request
     */
    public function approve(Request $request, BookingExtensionRequest $extensionRequest)
    {
        // Check authorization
        if ($extensionRequest->booking->vehicle->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Validate the request is still pending
        if ($extensionRequest->status !== 'pending') {
            return back()->withErrors([
                'request' => 'This extension request has already been processed.'
            ]);
        }

        $request->validate([
            'owner_notes' => 'nullable|string|max:500',
        ]);

        $extensionRequest->approve(Auth::user(), $request->owner_notes);

        return back()->with('success', 'Extension request approved successfully!');
    }

    /**
     * Reject an extension request
     */
    public function reject(Request $request, BookingExtensionRequest $extensionRequest)
    {
        // Check authorization
        if ($extensionRequest->booking->vehicle->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Validate the request is still pending
        if ($extensionRequest->status !== 'pending') {
            return back()->withErrors([
                'request' => 'This extension request has already been processed.'
            ]);
        }

        $request->validate([
            'owner_notes' => 'nullable|string|max:500',
        ]);

        $extensionRequest->reject(Auth::user(), $request->owner_notes);

        return back()->with('success', 'Extension request rejected.');
    }

    /**
     * Update extension settings for a vehicle
     */
    public function updateVehicleSettings(Request $request, $vehicleId)
    {
        $vehicle = \App\Models\Vehicle::where('id', $vehicleId)
            ->where('owner_id', Auth::id())
            ->firstOrFail();

        $request->validate([
            'allow_extensions' => 'required|boolean',
            'max_extension_hours' => 'required|integer|min:1|max:168', // Max 1 week
            'require_approval_for_extensions' => 'required|boolean',
        ]);

        $vehicle->update([
            'allow_extensions' => $request->allow_extensions,
            'max_extension_hours' => $request->max_extension_hours,
            'require_approval_for_extensions' => $request->require_approval_for_extensions,
        ]);

        return back()->with('success', 'Extension settings updated successfully!');
    }
}
