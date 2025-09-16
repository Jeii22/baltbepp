<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Get the authenticated user
        $user = $request->user();
        
        // Redirect based on user role
        if ($user->isSuperAdmin()) {
            $intended = $request->session()->get('url.intended');
            // If intended URL is for super admin area, allow it, otherwise go to dashboard
            if ($intended && (str_contains($intended, '/dashboard') || str_contains($intended, '/reports') || str_contains($intended, '/users') || str_contains($intended, '/trips') || str_contains($intended, '/fares') || str_contains($intended, '/bookings') || str_contains($intended, '/settings'))) {
                return redirect()->intended(route('dashboard', absolute: false));
            }
            return redirect()->route('dashboard')->with('success', 'Welcome back, Super Administrator!');
        } elseif ($user->isAdmin()) {
            // For now, redirect to welcome page until admin dashboard is created
            return redirect()->route('welcome')->with('success', 'Welcome back, Administrator!');
        } else {
            // Regular user/customer - redirect to welcome page
            return redirect()->route('welcome')->with('success', 'Welcome back!');
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
