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

        // Clear lock status on successful login
        $request->session()->forget('status');
        
        // Redirect based on user role
        if ($user->isSuperAdmin()) {
            return redirect()->route('superadmin.dashboard')->with('success', 'Welcome back, Super Administrator!');
        } elseif ($user->isAdmin()) {
            return redirect()->route('admin.dashboard')->with('success', 'Welcome back, Administrator!');
        } else {
            return redirect()->route('customer.dashboard')->with('success', 'Welcome back!');
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
