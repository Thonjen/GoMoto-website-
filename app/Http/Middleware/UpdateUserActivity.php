<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Update user activity if user is authenticated
        if ($request->user()) {
            $user = $request->user();
            
            // Only update if last seen was more than 1 minute ago to reduce DB calls
            if (!$user->last_seen_at || $user->last_seen_at->diffInMinutes(now()) >= 1) {
                $user->updateLastSeen();
            }
        }
        
        return $response;
    }
}
