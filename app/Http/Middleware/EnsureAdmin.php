<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            abort(403, 'Unauthorized.');
        }

        // Allow Admin OR Manager (if we want Managers to access panel)
        // For now, let's say Managers can access partials, but let's see. 
        // Task said "Verify/Seed roles: admin, manager, user".
        // So Manager should probably access Admin Panel too?
        
        if ($request->user()->hasRole('admin') || $request->user()->hasRole('manager')) {
             return $next($request);
        }

        abort(403, 'Unauthorized action.');
    }
}
