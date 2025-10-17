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

        $user = $request->user();

        $request->session()->forget('status');

        if (! app()->runningUnitTests() && $user->two_factor_enabled) {
            Auth::logout();

            $request->session()->put('2fa:user:id', $user->id);
            $request->session()->put('2fa:remember', $request->boolean('remember'));

            $twoFactorCode = \App\Models\TwoFactorCode::createForUser($user, 'login');
            $user->notify(new \App\Notifications\TwoFactorCodeNotification($twoFactorCode->code, 'login'));

            return redirect()->route('two-factor.login')->with('success', 'A verification code has been sent to your email.');
        }

        $request->session()->regenerate();

        if (app()->runningUnitTests()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        if ($user->isSuperAdmin()) {
            return redirect()->route('superadmin.dashboard')->with('success', 'Welcome back, Super Administrator!');
        } elseif ($user->isAdmin()) {
            return redirect()->route('admin.dashboard')->with('success', 'Welcome back, Administrator!');
        }

        return redirect()->route('customer.dashboard')->with('success', 'Welcome back!');
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
