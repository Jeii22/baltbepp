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

        if ($request->user()) {
            // Update last_active_at without touching updated_at
            $request->user()->forceFill(['last_active_at' => now()])->saveQuietly();
        }

        return $response;
    }
}
