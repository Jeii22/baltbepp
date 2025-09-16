<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsUser
{
    /**
     * Handle an incoming request.
     * This middleware ensures only regular users/customers can access certain routes
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->isUser()) {
            // Redirect admins to their appropriate dashboards
            if ($request->user()) {
                if ($request->user()->isSuperAdmin()) {
                    return redirect()->route('dashboard')->with('info', 'You are logged in as Super Admin.');
                } elseif ($request->user()->isAdmin()) {
                    // Redirect to admin dashboard when it exists
                    return redirect()->route('welcome')->with('info', 'You are logged in as Admin.');
                }
            }
            
            return redirect()->route('welcome')->with('error', 'Access denied.');
        }

        return $next($request);
    }
}
