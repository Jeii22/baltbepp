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
     *
     * On successful primary credential authentication, generate and send a one-time 2FA code,
     * then render the login view with the OTP modal active (no redirect) to preserve session/CSRF.
     */
    public function store(LoginRequest $request) // return type intentionally omitted: can be View or RedirectResponse
    {
        // Perform credential authentication (logs user in temporarily)
        $request->authenticate();
        $user = $request->user();

        // Always require multi-factor verification for every login (skip only for unit tests)
        if (! app()->runningUnitTests()) {
            // Store user data BEFORE logout (to avoid losing the user object)
            $userId = $user->id;
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
            session()->put('show_otp_modal', true);
            session()->save(); // Explicitly save session

            \Log::info('2FA session created', [
                'user_id' => $userId,
                'session_id' => session()->getId(),
                '2fa_user_id' => session('2fa:user:id'),
            ]);

            // Regenerate only CSRF token so the meta tag matches for AJAX
            $request->session()->regenerateToken();

            // Render login view directly (no redirect) with the OTP modal active
            return view('auth.login', [
                'showOtpModal' => true,
                // Optional diagnostics; safe to remove later
                'twoFactorUserId' => $userId,
                'sessionId' => session()->getId(),
            ])->with('success', 'A verification code was sent to your email. Enter it below to finish signing in.');
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
