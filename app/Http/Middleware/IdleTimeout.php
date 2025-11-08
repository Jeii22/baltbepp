<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IdleTimeout
{
    /**
     * Handle an incoming request.
     * If authenticated user has been idle longer than IDLE_TIMEOUT_MINUTES, terminate session.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $now = time();
            $last = session('last_activity_ts', $now);
            $limitMinutes = (int) env('IDLE_TIMEOUT_MINUTES', 30); // configurable
            $idleSeconds = $limitMinutes * 60;

            if ($last && ($now - $last) > $idleSeconds) {
                // Idle timeout reached: fully terminate session
                Auth::logout();
                $request->session()->flush();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->with('warning', 'Session expired due to inactivity. Please log in again.');
            }

            // Update timestamp for active session
            session(['last_activity_ts' => $now]);
        }

        return $next($request);
    }
}
