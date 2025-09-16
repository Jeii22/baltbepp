<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class RoleRedirectController extends Controller
{
    /**
     * Redirect user to appropriate dashboard based on their role
     */
    public function redirectToDashboard(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Redirect based on user role
        if ($user->isSuperAdmin()) {
            return redirect()->route('dashboard')->with('success', 'Welcome back, Super Administrator!');
        } elseif ($user->isAdmin()) {
            // For now, redirect to welcome page until admin dashboard is created
            return redirect()->route('welcome')->with('success', 'Welcome back, Administrator!');
        } else {
            // Regular user/customer
            return redirect()->route('welcome')->with('success', 'Welcome back!');
        }
    }

    /**
     * Handle access denied scenarios with appropriate redirects
     */
    public function handleAccessDenied(Request $request, string $requiredRole = null): RedirectResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to access this area.');
        }

        $message = match($requiredRole) {
            'super_admin' => 'Access denied. Super Administrator privileges required.',
            'admin' => 'Access denied. Administrator privileges required.',
            'user' => 'Access denied. This area is for customers only.',
            default => 'Access denied. You do not have permission to access this area.'
        };

        // Redirect to appropriate dashboard
        if ($user->isSuperAdmin()) {
            return redirect()->route('dashboard')->with('error', $message);
        } elseif ($user->isAdmin()) {
            return redirect()->route('welcome')->with('error', $message);
        } else {
            return redirect()->route('welcome')->with('error', $message);
        }
    }
}
