<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;

class UpdateBookingEndDatetime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:update-end-datetime';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update existing bookings with calculated end datetime';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating booking end datetimes...');
        
        $bookings = Booking::whereNull('calculated_end_datetime')
            ->with('pricingTier')
            ->get();
            
        $updated = 0;
        
        foreach ($bookings as $booking) {
            if ($booking->pickup_datetime && $booking->pricingTier) {
                $endDatetime = Booking::calculateEndDatetime($booking->pickup_datetime, $booking->pricingTier);
                $booking->calculated_end_datetime = $endDatetime;
                $booking->save();
                $updated++;
            }
        }
        
        $this->info("Updated {$updated} bookings.");
        
        return 0;
    }
}
