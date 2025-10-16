<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequirePasswordConfirmation
{
    /**
     * Handle an incoming request.
     * 
     * Require password confirmation for sensitive actions
     */
    public function handle(Request $request, Closure $next, int $maxAge = 10800): Response
    {
        // Check if password was confirmed recently (default: 3 hours)
        if ($this->shouldConfirmPassword($request, $maxAge)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Password confirmation required.',
                ], 423);
            }

            return redirect()->guest(
                route('password.confirm', [
                    'redirect' => $request->fullUrl(),
                ])
            );
        }

        return $next($request);
    }

    /**
     * Determine if the confirmation timeout has expired.
     */
    protected function shouldConfirmPassword(Request $request, int $maxAge): bool
    {
        $confirmedAt = $request->session()->get('auth.password_confirmed_at', 0);

        return time() - $confirmedAt > $maxAge;
    }
}