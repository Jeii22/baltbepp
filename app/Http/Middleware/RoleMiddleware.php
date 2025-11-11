<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Normalize roles (accept case/format variants)
        $userRole = strtolower(str_replace([' ', '-'], '_', trim((string) $request->user()->role)));
        $allowed = array_map(function($r){ return strtolower(str_replace([' ', '-'], '_', trim($r))); }, $roles);

        // Check if user has any of the allowed roles
        if (!in_array($userRole, $allowed)) {
            abort(403, 'Unauthorized access. You do not have permission to access this resource.');
        }

        return $next($request);
    }
}
