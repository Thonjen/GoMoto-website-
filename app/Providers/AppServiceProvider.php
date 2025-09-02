<?php

namespace App\Providers;
use Illuminate\Support\Facades\URL;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Observers\UserObserver;

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

        // Only force HTTPS in production or when specifically configured
        if (app()->environment('production') || config('app.force_https', false)) {
            URL::forceScheme('https');
        }
        
}
}