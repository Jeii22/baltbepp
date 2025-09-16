<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->isSuperAdmin()) {
            // Redirect to appropriate dashboard or show access denied
            if ($request->user()) {
                if ($request->user()->isAdmin()) {
                    // Redirect admin to admin dashboard (when it exists)
                    return redirect()->route('welcome')->with('error', 'Access denied. You are an Admin, not a Super Admin.');
                } elseif ($request->user()->isUser()) {
                    return redirect()->route('welcome')->with('error', 'Access denied. You do not have super admin privileges.');
                }
            }
            
            abort(403, 'Access denied. Super Admin privileges required.');
        }

        return $next($request);
    }
}
