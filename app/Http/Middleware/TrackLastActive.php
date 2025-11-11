<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackLastActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only track if user is authenticated and session is active
        if (auth()->check() && $request->user()) {
            try {
                // Update last_active_at without touching updated_at
                $request->user()->forceFill(['last_active_at' => now()])->saveQuietly();
            } catch (\Exception $e) {
                // Silently fail to prevent blocking requests
                \Log::warning('Failed to update last_active_at', ['error' => $e->getMessage()]);
            }
        }

        return $response;
    }
}
