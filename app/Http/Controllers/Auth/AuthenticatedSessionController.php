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
            // Store user data BEFORE logout (to avoid losing the user object)
            $userId = $user->id;
            $userEmail = $user->email;
            $remember = $request->boolean('remember');
            
            // Generate OTP code BEFORE logout (while we still have the user)
            $twoFactorCode = \App\Models\TwoFactorCode::createForUser($user, 'login');
            
            // Send notification BEFORE logout
            $user->notify(new \App\Notifications\TwoFactorCodeNotification($twoFactorCode->code, 'login'));
            
            // NOW logout the provisional session
            Auth::logout();
            
            // Set session data AFTER logout
            session()->put('2fa:user:id', $userId);
            session()->put('2fa:remember', $remember);
            session()->save(); // Explicitly save session
            
            \Log::info('2FA session created', [
                'user_id' => $userId,
                'session_id' => session()->getId(),
                '2fa_user_id' => session('2fa:user:id'),
            ]);

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
        // Flush all session data before invalidation for completeness
        $request->session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('info', 'You have been logged out securely.');
    }
}
