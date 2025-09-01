<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            \App\Http\Middleware\KycWarningMiddleware::class,
        ]);
        
        // Add banned user check to auth middleware group
        $middleware->appendToGroup('auth', [
            \App\Http\Middleware\CheckBannedUser::class,
            \App\Http\Middleware\UpdateUserActivity::class,
        ]);
        
        // Sanctum SPA support - should be in API middleware, not web
        $middleware->api(prepend: [
            EnsureFrontendRequestsAreStateful::class,
        ]);
        
        // Add CORS middleware globally
        $middleware->append(\Illuminate\Http\Middleware\HandleCors::class);
        
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'kyc.verified' => \App\Http\Middleware\KycVerifiedMiddleware::class,
            'kyc.warning' => \App\Http\Middleware\KycWarningMiddleware::class,
            'check.banned' => \App\Http\Middleware\CheckBannedUser::class,
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
        ]);
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
