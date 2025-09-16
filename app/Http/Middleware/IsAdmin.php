<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->isAdmin()) {
            // Redirect to appropriate dashboard or show access denied
            if ($request->user()) {
                if ($request->user()->isSuperAdmin()) {
                    return redirect()->route('dashboard')->with('error', 'Access denied. You are a Super Admin, not a regular Admin.');
                } elseif ($request->user()->isUser()) {
                    return redirect()->route('welcome')->with('error', 'Access denied. You do not have admin privileges.');
                }
            }
            
            abort(403, 'Access denied. Admin privileges required.');
        }

        return $next($request);
    }
}
