<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();
        
        if (!$user) {
            abort(403, 'Unauthorized');
        }
        
        // Check if user is banned or suspended
        if (in_array($user->status, ['banned', 'suspended'])) {
            abort(403, 'Your account access has been restricted.');
        }
        
        // Check role permissions
        if (!$user->role || !in_array($user->role->name, $roles)) {
            abort(403, 'Unauthorized');
        }
        
        return $next($request);
    }
}
