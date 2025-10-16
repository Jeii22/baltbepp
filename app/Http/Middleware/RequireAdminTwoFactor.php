<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireAdminTwoFactor
{
    /**
     * Handle an incoming request.
     * 
     * Enforce 2FA for admin and super_admin users
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        // Only enforce for admin and super_admin roles
        if ($user && in_array($user->role, ['admin', 'super_admin'])) {
            // Check if 2FA is enabled
            if (!$user->two_factor_enabled) {
                // Redirect to profile with warning
                return redirect()
                    ->route('profile.edit')
                    ->with('warning', 'Two-Factor Authentication is required for admin accounts. Please enable it to continue.');
            }
        }
        
        return $next($request);
    }
}