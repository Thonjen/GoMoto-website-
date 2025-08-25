<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class TestEmailVerification extends Command
{
    protected $signature = 'test:email-verification';
    protected $description = 'Test email verification functionality';

    public function handle()
    {
        $this->info('Testing Email Verification Setup...');
        
        // Check mail configuration
        $this->info('Mail Driver: ' . config('mail.default'));
        $this->info('Mail Host: ' . config('mail.mailers.smtp.host'));
        $this->info('Mail Port: ' . config('mail.mailers.smtp.port'));
        $this->info('From Address: ' . config('mail.from.address'));
        
        // Create a test user
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'email_verified_at' => null
        ]);
        
        $this->info("Created test user with email: {$user->email}");
        
        try {
            // Send verification email
            $user->sendEmailVerificationNotification();
            $this->info('✅ Email verification sent successfully!');
            
            if (config('mail.default') === 'log') {
                $this->warn('⚠️  Currently using LOG driver - check storage/logs/laravel.log for email content');
            } else {
                $this->info('📧 Email sent via ' . config('mail.default') . ' driver');
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Failed to send email: ' . $e->getMessage());
        }
        
        // Clean up
        $user->delete();
        $this->info('Test user cleaned up');
    }
}
