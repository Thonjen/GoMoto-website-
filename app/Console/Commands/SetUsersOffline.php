<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;

class SetUsersOffline extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:set-offline {--minutes=5 : Minutes of inactivity before setting offline}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set users offline who have been inactive for a specified period';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $minutes = $this->option('minutes');
        $cutoffTime = Carbon::now()->subMinutes($minutes);
        
        $usersSetOffline = User::where('is_online', true)
            ->where(function ($query) use ($cutoffTime) {
                $query->whereNull('last_seen_at')
                      ->orWhere('last_seen_at', '<', $cutoffTime);
            })
            ->update(['is_online' => false]);
        
        $this->info("Set {$usersSetOffline} users offline who were inactive for more than {$minutes} minutes.");
        
        return Command::SUCCESS;
    }
}
