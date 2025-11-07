<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule the command to set users offline every 5 minutes
Schedule::command('users:set-offline')->everyFiveMinutes();

// Schedule return reminder SMS to be sent every minute (to catch the 10-minute window accurately)
Schedule::command('bookings:send-return-reminders')->everyMinute();
