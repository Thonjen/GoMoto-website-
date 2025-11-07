<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class SmsService
{
    public function send(string $to, string $message): bool
    {
        // Validate recipient
        if (empty($to)) {
            Log::warning('SmsService: no phone number provided');
            return false;
        }

        $apiKey = config('sms.api_key');
        $sender = config('sms.name');

        if (empty($apiKey)) {
            Log::error('SmsService: missing Semaphore API key');
            return false;
        }

        if (empty($sender)) {
            Log::warning('SmsService: missing sender name; proceeding without explicit sender');
        }

        $number = $this->normalizeNumber($to);
        $preparedMessage = $this->prepareMessage($message);
        $senderName = $this->prepareSenderName($sender);

        Log::info('SmsService: attempting to send SMS via Semaphore', [
            'to' => $number,
            'sender' => $senderName,
            'preview' => mb_substr($preparedMessage, 0, 120),
        ]);

        try {
            // Semaphore v4 Messages endpoint
            $response = Http::timeout(15)
                ->asForm()
                ->post('https://api.semaphore.co/api/v4/messages', array_filter([
                    'apikey' => $apiKey,
                    'sendername' => $senderName, // optional but recommended (max 11 chars)
                    'number' => $number,
                    'message' => $preparedMessage,
                ]));

            if ($response->successful()) {
                Log::info('SmsService: SMS sent successfully via Semaphore', [
                    'to' => $number,
                    'status' => $response->status(),
                    'response' => $response->json(),
                ]);
                return true;
            }

            Log::warning('SmsService: SMS send failed via Semaphore', [
                'to' => $number,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return false;

        } catch (\Throwable $e) {
            Log::error('SmsService: exception when sending SMS via Semaphore', [
                'to' => $number ?? $to,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function sendVerificationToUser(User $user): bool
    {
        $message = "Hi {$user->first_name}, your identity has been verified. You can now access all verified features on GoMoto.";

        Log::info('SmsService: sending verification SMS to user', [
            'user_id' => $user->id,
            'phone' => $user->phone
        ]);

        return $this->send($user->phone, $message);
    }

    /**
     * Scenario 1: Send SMS when owner accepts/confirms a booking request
     */
    public function sendBookingConfirmation($booking): bool
    {
        $user = $booking->user;
        $vehicle = $booking->vehicle;
        $pricingTier = $booking->pricingTier;
        
        // Format vehicle name
        $vehicleName = "{$vehicle->make->name} {$vehicle->model->name}";
        $vehicleType = $vehicle->type->sub_type ?? $vehicle->vehicleType->type ?? 'Vehicle';
        
        // Format dates
        $pickupDate = \Carbon\Carbon::parse($booking->pickup_datetime)->format('M d, Y g:i A');
        $expectedReturn = $booking->getCalculatedEndDatetimeAttribute();
        $returnDate = $expectedReturn ? $expectedReturn->format('M d, Y g:i A') : 'TBD';
        
        // Get pickup location (use vehicle location)
        $pickupLocation = $vehicle->location ?? 'Please contact owner for location';
        
        $message = "Hi {$user->first_name}! Your booking has been CONFIRMED by the owner.\n\n"
                 . "Vehicle: {$vehicleName} ({$vehicleType})\n"
                 . "License Plate: {$vehicle->license_plate}\n"
                 . "Pickup: {$pickupDate}\n"
                 . "Return: {$returnDate}\n"
                 . "Location: {$pickupLocation}\n"
                 . "Amount: ₱" . number_format($booking->total_amount, 2) . "\n\n"
                 . "Please arrive on time. Contact owner: {$vehicle->owner->phone}\n"
                 . "Thank you for using GoMoto!";

        Log::info('SmsService: sending booking confirmation SMS', [
            'booking_id' => $booking->id,
            'user_id' => $user->id,
            'phone' => $user->phone
        ]);

        return $this->send($user->phone, $message);
    }

    /**
     * Scenario 2: Send SMS warning 10 minutes before expected return time
     */
    public function sendReturnReminder($booking): bool
    {
        $user = $booking->user;
        $vehicle = $booking->vehicle;
        
        $vehicleName = "{$vehicle->make->name} {$vehicle->model->name}";
        $expectedReturn = $booking->getCalculatedEndDatetimeAttribute();
        $returnTime = $expectedReturn ? $expectedReturn->format('g:i A') : 'scheduled time';
        
        $message = "REMINDER: {$user->first_name}, your {$vehicleName} rental is due in 10 MINUTES at {$returnTime}.\n\n"
                 . "Please return the vehicle on time to avoid overcharge fees. "
                 . "If you need more time, request an extension through the app.\n\n"
                 . "Late returns may incur additional charges as per the rental agreement.\n"
                 . "GoMoto";

        Log::info('SmsService: sending return reminder SMS', [
            'booking_id' => $booking->id,
            'user_id' => $user->id,
            'phone' => $user->phone
        ]);

        return $this->send($user->phone, $message);
    }

    /**
     * Scenario 3: Send SMS when owner marks booking as complete
     */
    public function sendBookingCompletion($booking, $overchargeAmount = 0): bool
    {
        $user = $booking->user;
        $vehicle = $booking->vehicle;
        
        $vehicleName = "{$vehicle->make->name} {$vehicle->model->name}";
        $returnTime = $booking->return_time ? \Carbon\Carbon::parse($booking->return_time)->format('M d, Y g:i A') : now()->format('M d, Y g:i A');
        
        $message = "Hi {$user->first_name}! Your {$vehicleName} rental has been successfully COMPLETED.\n\n"
                 . "Return Time: {$returnTime}\n"
                 . "Initial Amount: ₱" . number_format($booking->total_amount, 2);
        
        if ($overchargeAmount > 0) {
            $message .= "\nOvercharge Applied: ₱" . number_format($overchargeAmount, 2);
            $message .= "\nTotal: ₱" . number_format($booking->total_amount + $overchargeAmount, 2);
            $message .= "\n\nPlease settle the overcharge payment with the owner.";
        } else {
            $message .= "\n\nNo additional charges applied. Thank you for returning on time!";
        }
        
        $message .= "\n\nThank you for using GoMoto. We hope to serve you again!";

        Log::info('SmsService: sending booking completion SMS', [
            'booking_id' => $booking->id,
            'user_id' => $user->id,
            'phone' => $user->phone,
            'overcharge_amount' => $overchargeAmount
        ]);

        return $this->send($user->phone, $message);
    }

    /**
     * Normalize PH mobile numbers to 63XXXXXXXXXX format as recommended for local gateways.
     */
    private function normalizeNumber(string $raw): string
    {
        $n = trim($raw);
        // Remove spaces, dashes, parentheses
        $n = preg_replace('/[^\d+]/', '', $n);

        // If starts with +63, convert to 63
        if (strpos($n, '+63') === 0) {
            $n = '63' . substr($n, 3);
        }
        // If starts with 0 (e.g., 09XXXXXXXXX) -> 63XXXXXXXXXX
        if (strpos($n, '0') === 0) {
            $n = '63' . substr($n, 1);
        }
        // If already starts with 63 - keep
        // Otherwise, leave as-is (gateway may handle)
        return $n;
    }

    /**
     * Prepare message per Semaphore guidelines:
     * - Use GSM-friendly characters
     * - Keep length reasonable (Semaphore supports concatenated SMS; cap at ~700 chars)
     */
    private function prepareMessage(string $message): string
    {
        $msg = trim($message);
        // Soft cap length to avoid excessive concatenation (charged per segment)
        $max = 700; // roughly 4–5 segments
        if (mb_strlen($msg) > $max) {
            $msg = mb_substr($msg, 0, $max - 3) . '...';
        }
        return $msg;
    }

    /**
     * Ensure sender name complies with Semaphore rules (max 11 chars).
     */
    private function prepareSenderName(?string $sender): ?string
    {
        if (!$sender) {
            return null; // allow API default
        }
        $s = trim($sender);
        if (mb_strlen($s) > 11) {
            Log::warning('SmsService: sender name exceeds 11 chars, truncating', [
                'original' => $sender,
            ]);
            $s = mb_substr($s, 0, 11);
        }
        return $s;
    }
}
