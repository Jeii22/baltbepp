<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountLocked
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check lock status for authenticated users
        if (auth()->check() && $request->user() && $request->user()->isLocked()) {
            $lockedUntil = \Carbon\Carbon::parse($request->user()->locked_until);
            $minutes = now()->diffInMinutes($lockedUntil);
            
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => "Your account has been locked due to multiple failed login attempts. Please try again in {$minutes} minutes."
            ]);
        }

        return $next($request);
    }
}
