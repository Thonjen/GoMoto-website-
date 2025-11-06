<?php

namespace App\Providers;
use Illuminate\Support\Facades\URL;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;
use Illuminate\Mail\Events\MessageSent;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
        
        // Register User observer
        User::observe(UserObserver::class);
        
        // Register authentication event listeners
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Login::class,
            \App\Listeners\UpdateUserActivityOnLogin::class
        );
        
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Logout::class,
            \App\Listeners\UpdateUserActivityOnLogout::class
        );

        // Listen for successful mail sends and log recipient/subject to laravel.log
        Event::listen(MessageSent::class, function (MessageSent $event) {
            try {
                $message = $event->message;

                $to = [];
                // Swift_Mime_Message (SwiftMailer) returns array from getTo()
                if (method_exists($message, 'getTo')) {
                    $rawTo = $message->getTo();
                    if (is_array($rawTo)) {
                        foreach ($rawTo as $key => $val) {
                            // Swift returns ['email' => 'Name'] or similar
                            if (is_string($key)) {
                                $to[] = $key;
                            } elseif (is_object($val) && method_exists($val, 'getAddress')) {
                                $to[] = $val->getAddress();
                            }
                        }
                    }
                }

                $subject = method_exists($message, 'getSubject') ? $message->getSubject() : null;

                Log::info('Mail successfully sent', [
                    'to' => $to,
                    'subject' => $subject,
                ]);
            } catch (\Throwable $e) {
                // Ensure logging does not break the app if something unexpected happens
                Log::error('Error while logging sent mail: '.$e->getMessage());
            }
        });

        // Only force HTTPS in production or when specifically configured
        if (app()->environment('production') || config('app.force_https', false)) {
            URL::forceScheme('https');
        }
        
}
}