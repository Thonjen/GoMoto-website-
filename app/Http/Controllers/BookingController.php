<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\Payment;
use App\Models\PaymentMode;
use App\Models\VehiclePricingTier;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::where('user_id', Auth::id())
            ->with(['vehicle.make', 'vehicle.model', 'vehicle.type', 'vehicle.fuelType', 'vehicle.transmission', 'pricingTier', 'payment.paymentMode'])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Booking/Index', [
            'bookings' => $bookings,
        ]);
    }

    public function show(Booking $booking)
    {
        // Check if user owns this booking or is an admin
        $isOwner = $booking->user_id === Auth::id();
        $isAdmin = Auth::user()->role && Auth::user()->role->name === 'admin';
        
        if (!$isOwner && !$isAdmin) {
            abort(403);
        }

        $booking->load([
            'vehicle.make', 
            'vehicle.model',
            'vehicle.type', 
            'vehicle.owner',
            'pricingTier', 
            'payment.paymentMode',
            'vehicle.transmission',
            'vehicle.fuelType',
            'vehicle.brand',
            'vehicle.vehicleType',
            'overcharges.overchargeType',
            'pendingExtensionRequest',
            'extensionRequests' => function($query) {
                $query->orderBy('created_at', 'desc');
            },
            'rating' // Include rating relationship
        ]);

        // Calculate expected return time
        $expectedReturnTime = $booking->getCalculatedEndDatetimeAttribute();
        
        // Check for potential overcharges (if booking is still active)
        $potentialOvercharges = [];
        if ($booking->status === 'confirmed' && !$booking->return_time) {
            $potentialOvercharges = $booking->calculateOvercharges();
        }

        return Inertia::render('Booking/Show', [
            'booking' => $booking,
            'expectedReturnTime' => $expectedReturnTime,
            'potentialOvercharges' => $potentialOvercharges,
        ]);
    }

    public function create(Vehicle $vehicle)
    {
        $vehicle->load(['make', 'model', 'type', 'fuelType', 'transmission', 'pricingTiers', 'owner']);
        
        // Get only the payment methods the owner accepts
        $availablePaymentMethods = $vehicle->owner->getAvailablePaymentMethods();
        
        // Check if user already has active bookings for this vehicle
        $userActiveBookings = Booking::getUserActiveBookingsForVehicle(Auth::id(), $vehicle->id);
        
        return Inertia::render('Booking/Create', [
            'vehicle' => $vehicle,
            'availablePaymentMethods' => $availablePaymentMethods,
            'userActiveBookings' => $userActiveBookings,
        ]);
    }

    public function store(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'pricing_tier_id' => 'required|exists:vehicle_pricing_tiers,id',
            'pickup_date' => 'required|date|after_or_equal:today',
            'pickup_time' => 'required|date_format:H:i',
            'payment_mode_id' => 'required|exists:payment_modes,id',
            'reference_number' => 'nullable|string|max:100',
        ]);

        // Verify the pricing tier belongs to this vehicle
        $pricingTier = VehiclePricingTier::where('id', $request->pricing_tier_id)->first();
        
        // Check if this pricing tier is actually available for this vehicle
        $isValidTier = $vehicle->pricingTiers()->where('vehicle_pricing_tiers.id', $request->pricing_tier_id)->exists();
        
        if (!$pricingTier || !$isValidTier) {
            return back()->withErrors(['pricing_tier_id' => 'Invalid pricing tier for this vehicle.']);
        }

        // Verify the owner accepts the selected payment method
        $paymentMode = PaymentMode::find($request->payment_mode_id);
        $availablePaymentMethods = $vehicle->owner->getAvailablePaymentMethods();
        $acceptedPaymentIds = collect($availablePaymentMethods)->pluck('id')->toArray();
        
        if (!in_array($request->payment_mode_id, $acceptedPaymentIds)) {
            return back()->withErrors(['payment_mode_id' => 'This payment method is not accepted by the vehicle owner.']);
        }

        $pickupDatetime = Carbon::createFromFormat('Y-m-d H:i', $request->pickup_date . ' ' . $request->pickup_time);

        // Check for double booking conflicts with database locking
        return DB::transaction(function () use ($request, $vehicle, $pricingTier, $pickupDatetime, $paymentMode) {
            // Lock the vehicle row to prevent concurrent bookings
            $lockedVehicle = Vehicle::lockForUpdate()->find($vehicle->id);
            
            // Check if user already has an active booking for this vehicle during overlapping time
            $conflictingUserBooking = Booking::userHasActiveBooking(Auth::id(), $vehicle->id, $pickupDatetime, $pricingTier);
            if ($conflictingUserBooking) {
                return back()->withErrors([
                    'user_booking_conflict' => 'You already have a pending or confirmed booking for this vehicle during this time period. Please cancel your existing booking first or choose a different time.'
                ]);
            }
            
            // Check for booking conflicts with other users
            if (Booking::hasConflict($vehicle->id, $pickupDatetime, $pricingTier)) {
                return back()->withErrors([
                    'booking_conflict' => 'This vehicle is already booked for the selected time period. Please choose a different time or vehicle.'
                ]);
            }

            // Double-check vehicle availability
            if (!$lockedVehicle->is_available) {
                return back()->withErrors([
                    'vehicle_unavailable' => 'This vehicle is currently unavailable for booking.'
                ]);
            }

            // Create booking
            $booking = Booking::create([
                'user_id' => Auth::id(),
                'vehicle_id' => $vehicle->id,
                'pricing_tier_id' => $pricingTier->id,
                'pickup_datetime' => $pickupDatetime,
                'total_amount' => $pricingTier->price,
                'status' => 'pending',
            ]);

            // Create payment record
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'payment_mode_id' => $request->payment_mode_id,
                'type' => $paymentMode->name === 'GCash QR' ? 'gcash' : 'cod',
                'amount' => $pricingTier->price,
                'reference_number' => $request->reference_number,
                'receipt_image' => null,
                'paid_at' => null,
            ]);

            return redirect()->route('bookings.payment', $booking->id)
                ->with('success', 'Booking created successfully!');
        });
    }

    public function payment(Booking $booking)
    {
        // Check if user owns this booking
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $booking->load([
            'vehicle.owner', 
            'vehicle.make',
            'vehicle.model', 
            'vehicle.type',
            'vehicle.fuelType',
            'vehicle.transmission',
            'pricingTier', 
            'payment.paymentMode'
        ]);

        return Inertia::render('Booking/Payment', [
            'booking' => $booking,
        ]);
    }

    public function uploadReceipt(Request $request, Booking $booking)
    {
        // Check if user owns this booking
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'receipt_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $path = $request->file('receipt_image')->store('payment_receipts', 'public');

        $booking->payment->update([
            'receipt_image' => '/storage/' . $path,
        ]);

        return back()->with('success', 'Receipt uploaded successfully! Awaiting confirmation.');
    }

    // Owner methods
    public function ownerIndex()
    {
        $bookings = Booking::whereHas('vehicle', function ($query) {
            $query->where('owner_id', Auth::id());
        })
        ->with(['user', 'vehicle.make', 'vehicle.model', 'vehicle.type', 'vehicle.fuelType', 'vehicle.transmission', 'pricingTier', 'payment.paymentMode'])
        ->orderBy('created_at', 'desc')
        ->get();

        return Inertia::render('Owner/Booking/Index', [
            'bookings' => $bookings,
        ]);
    }

    public function ownerCalendar()
    {
        $bookings = Booking::whereHas('vehicle', function ($query) {
            $query->where('owner_id', Auth::id());
        })
        ->with(['user', 'vehicle.make', 'vehicle.model', 'vehicle.type', 'vehicle.fuelType', 'vehicle.transmission', 'pricingTier'])
        ->whereIn('status', ['pending', 'confirmed', 'completed'])
        ->get();

        // Transform bookings into FullCalendar events
        $events = $bookings->map(function ($booking) {
            $endDateTime = $booking->calculated_end_datetime;
            
            // If calculated_end_datetime is null, calculate it
            if (!$endDateTime) {
                $endDateTime = Booking::calculateEndDatetime($booking->pickup_datetime, $booking->pricingTier);
            }

            $color = match($booking->status) {
                'pending' => '#f59e0b', // yellow
                'confirmed' => '#10b981', // green  
                'completed' => '#6b7280', // gray
                'cancelled' => '#ef4444', // red
                default => '#3b82f6' // blue
            };

            return [
                'id' => $booking->id,
                'title' => "{$booking->vehicle->make->name} {$booking->vehicle->model->name} - {$booking->user->first_name} {$booking->user->last_name}",
                'start' => $booking->pickup_datetime->toISOString(),
                'end' => $endDateTime->toISOString(),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'booking_id' => $booking->id,
                    'status' => $booking->status,
                    'vehicle' => [
                        'id' => $booking->vehicle->id,
                        'license_plate' => $booking->vehicle->license_plate,
                        'make' => $booking->vehicle->make->name,
                        'model' => $booking->vehicle->model->name,
                        'type' => $booking->vehicle->type->sub_type,
                    ],
                    'user' => [
                        'name' => $booking->user->first_name . ' ' . $booking->user->last_name,
                        'email' => $booking->user->email,
                        'phone' => $booking->user->phone,
                    ],
                    'duration' => $booking->pricingTier->duration_from . ' ' . $booking->pricingTier->duration_unit,
                    'amount' => $booking->total_amount,
                ]
            ];
        });

        $vehicles = Vehicle::where('owner_id', Auth::id())->with(['make', 'model', 'type'])->get();

        return Inertia::render('Owner/Booking/Calendar', [
            'events' => $events,
            'vehicles' => $vehicles,
        ]);
    }

    public function ownerShow(Booking $booking)
    {
        // Check if user owns the vehicle for this booking
        if ($booking->vehicle->owner_id !== Auth::id()) {
            abort(403);
        }

        $booking->load([
            'user',
            'vehicle.make',
            'vehicle.model', 
            'vehicle.type',
            'vehicle.fuelType',
            'vehicle.transmission',
            'pricingTier', 
            'payment.paymentMode',
            'overcharges.overchargeType'
        ]);

        // Calculate expected return time
        $expectedReturnTime = $booking->getCalculatedEndDatetimeAttribute();
        
        // Check for potential overcharges if booking is active
        $potentialOvercharges = [];
        if ($booking->status === 'confirmed' && !$booking->return_time) {
            $potentialOvercharges = $booking->calculateOvercharges();
        }

        return Inertia::render('Owner/Booking/Show', [
            'booking' => $booking,
            'expectedReturnTime' => $expectedReturnTime,
            'potentialOvercharges' => $potentialOvercharges,
        ]);
    }

    public function confirm(Booking $booking)
    {
        // Check if user owns the vehicle for this booking
        if ($booking->vehicle->owner_id !== Auth::id()) {
            abort(403);
        }

        $booking->update(['status' => 'confirmed']);
        
        // If it's a COD booking, mark payment as paid
        if ($booking->payment->type === 'cod') {
            $booking->payment->update(['paid_at' => now()]);
        }

        return back()->with('success', 'Booking confirmed successfully!');
    }

    public function reject(Booking $booking)
    {
        // Check if user owns the vehicle for this booking
        if ($booking->vehicle->owner_id !== Auth::id()) {
            abort(403);
        }

        $booking->update(['status' => 'cancelled']);

        return back()->with('success', 'Booking rejected.');
    }

    public function confirmPayment(Booking $booking)
    {
        // Check if user owns the vehicle for this booking
        if ($booking->vehicle->owner_id !== Auth::id()) {
            abort(403);
        }

        $booking->payment->update(['paid_at' => now()]);
        $booking->update(['status' => 'confirmed']);

        return back()->with('success', 'Payment confirmed and booking approved!');
    }

    public function complete(Request $request, Booking $booking)
    {
        // Check if user owns the vehicle for this booking
        if ($booking->vehicle->owner_id !== Auth::id()) {
            abort(403);
        }

        // Validate return location data
        $request->validate([
            'return_latitude' => 'nullable|numeric',
            'return_longitude' => 'nullable|numeric',
            'return_location_name' => 'nullable|string'
        ]);

        // Update booking with return information
        $booking->update([
            'status' => 'completed',
            'return_time' => now(),
            'return_latitude' => $request->return_latitude,
            'return_longitude' => $request->return_longitude,
            'return_location_name' => $request->return_location_name,
        ]);

        // Calculate and apply overcharges
        $overchargeAmount = $booking->applyOvercharges();
        
        // Make vehicle available again
        $booking->vehicle->update(['is_available' => true]);

        $message = 'Booking marked as completed!';
        if ($overchargeAmount > 0) {
            $message .= " Overcharges of ₱{$overchargeAmount} have been applied.";
        }

        return back()->with('success', $message);
    }

    public function cancel(Booking $booking)
    {
        // Check if user owns this booking or is an admin
        $isOwner = $booking->user_id === Auth::id();
        $isAdmin = Auth::user()->role && Auth::user()->role->name === 'admin';
        
        if (!$isOwner && !$isAdmin) {
            abort(403);
        }

        // Only allow canceling pending bookings
        if ($booking->status !== 'pending') {
            return back()->withErrors(['booking' => 'Only pending bookings can be cancelled.']);
        }

        $booking->update(['status' => 'cancelled']);

        return back()->with('success', 'Booking cancelled successfully.');
    }
}
