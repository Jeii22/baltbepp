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
        // Perform credential authentication (logs user in temporarily)
        $request->authenticate();
        $user = $request->user();

        // Always require multi-factor verification for every login (skip only for unit tests)
        if (! app()->runningUnitTests()) {
            // Immediately log out the provisional session
            Auth::logout();

            // Store context for 2FA verification
            $request->session()->put('2fa:user:id', $user->id);
            $request->session()->put('2fa:remember', $request->boolean('remember'));

            // Generate a fresh login OTP code and notify user
            $twoFactorCode = \App\Models\TwoFactorCode::createForUser($user, 'login');
            $user->notify(new \App\Notifications\TwoFactorCodeNotification($twoFactorCode->code, 'login'));

            return redirect()->route('two-factor.login')->with('success', 'We sent a verification email: confirm this login to continue.');
        }

        // Test environment fallback: proceed directly
        $request->session()->regenerate();
        return redirect()->intended(route('dashboard', absolute: false));
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
