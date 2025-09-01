<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified as LaravelEnsureEmailIsVerified;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified extends LaravelEnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $redirectToRoute
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle($request, Closure $next, $redirectToRoute = null): Response
    {
        $user = $request->user();
        
        // Admin users bypass email verification
        if ($user && $user->role && $user->role->name === 'admin') {
            return $next($request);
        }
        
        // Use Laravel's default email verification logic for non-admin users
        return parent::handle($request, $next, $redirectToRoute);
    }
}
