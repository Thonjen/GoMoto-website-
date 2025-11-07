<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Services\SmsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendReturnReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:send-return-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send SMS reminders to users 10 minutes before their booking return time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $smsService = app(SmsService::class);
        
        // Get all confirmed bookings that haven't been returned yet
        $activeBookings = Booking::where('status', 'confirmed')
            ->whereNull('return_time')
            ->with(['user', 'vehicle.make', 'vehicle.model', 'pricingTier'])
            ->get();

        $now = Carbon::now();
        $remindersSent = 0;

        foreach ($activeBookings as $booking) {
            // Calculate expected return time
            $expectedReturn = $booking->getCalculatedEndDatetimeAttribute();
            
            if (!$expectedReturn) {
                continue;
            }

            // Calculate time difference in minutes
            $minutesUntilReturn = $now->diffInMinutes($expectedReturn, false);

            // Send reminder if between 10 and 11 minutes before return time
            // (This prevents sending multiple reminders if the command runs multiple times)
            if ($minutesUntilReturn >= 10 && $minutesUntilReturn <= 11) {
                // Check if we already sent a reminder (you can add a field to track this)
                // For now, we'll just send it
                try {
                    $smsService->sendReturnReminder($booking);
                    $remindersSent++;
                    
                    $this->info("Sent return reminder to {$booking->user->first_name} {$booking->user->last_name} (Booking #{$booking->id})");
                    
                    Log::info('Return reminder sent', [
                        'booking_id' => $booking->id,
                        'user_id' => $booking->user_id,
                        'expected_return' => $expectedReturn->toDateTimeString(),
                    ]);
                } catch (\Exception $e) {
                    $this->error("Failed to send reminder for Booking #{$booking->id}: {$e->getMessage()}");
                    
                    Log::error('Failed to send return reminder', [
                        'booking_id' => $booking->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        $this->info("Total return reminders sent: {$remindersSent}");
        
        return 0;
    }
}
