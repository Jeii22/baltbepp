<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\TwoFactorCode;
use App\Models\User;
use App\Notifications\TwoFactorCodeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TwoFactorController extends Controller
{
    /**
     * Show the 2FA verification form
     */
    public function show(Request $request)
    {
        // Check if 2FA session exists
        $userId = session('2fa:user:id');
        
        // If session missing we still show challenge (user may use signed link that bypasses session)
        if (!$userId) {
            \Log::info('2FA challenge viewed without session; awaiting signed link or fresh login', [
                'session_id' => session()->getId(),
                'ip' => $request->ip(),
            ]);
        }

        // Support direct code param ONLY if session user exists (fallback convenience)
        $code = $request->query('code');
        if ($code && $userId) {
            $user = User::find($userId);
            if ($user && TwoFactorCode::verify($user, $code, 'login')) {
                $this->completeLogin($request, $user);
                return redirect()->to($this->targetUrlFor($user))->with('success', 'Login verified. Welcome back!');
            }
        }

        return view('auth.two-factor-challenge');
    }

    /**
     * Verify the 2FA code
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $userId = session('2fa:user:id');
        
        if (!$userId) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session expired. Please login again.'
                ], 401);
            }
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }
        
        $user = User::findOrFail($userId);

        // Extra diagnostic logging
        \Log::info('2FA verify attempt', [
            'user_id' => $user->id,
            'code_provided' => $request->code,
            'has_existing_codes' => TwoFactorCode::where('user_id',$user->id)->exists(),
        ]);

        if (!TwoFactorCode::verify($user, $request->code, 'login')) {
            \Log::warning('2FA verify failed', [
                'user_id' => $user->id,
                'code_provided' => $request->code,
            ]);
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'The verification code is invalid or has expired.'
                ], 422);
            }
            
            throw ValidationException::withMessages([
                'code' => 'The verification code is invalid or has expired.',
            ]);
        }

        \Log::info('2FA verify success', [
            'user_id' => $user->id,
        ]);

        // Clear 2FA session data
        session()->forget('2fa:user:id');

        // Mark email as verified and enable 2FA after successful verification
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }
        
        if (!$user->two_factor_enabled) {
            $user->update(['two_factor_enabled' => true]);
        }

        // Log the user in
        Auth::login($user, session('2fa:remember', false));
        session()->forget('2fa:remember');

        $request->session()->regenerate();

        // Confirm session regeneration and auth
        \Log::info('2FA post-login', [
            'user_id' => $user->id,
            'authenticated' => auth()->check(),
        ]);

        // Update last login
        $user->updateLastLogin($request->ip());

        // Return JSON for AJAX or redirect for regular form
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Login verified successfully.',
                'redirect' => $this->targetUrlFor($user)
            ]);
        }

        // Redirect based on role
        return redirect()->to($this->targetUrlFor($user))->with('success', 'Your account has been verified and 2FA is now enabled.');
    }

    /**
     * Resend the 2FA code
     */
    public function resend(Request $request)
    {
        $userId = session('2fa:user:id');
        
        if (!$userId) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session expired. Please login again.'
                ], 401);
            }
            return redirect()->route('login');
        }

        $user = User::findOrFail($userId);

        // Generate new code
        $twoFactorCode = TwoFactorCode::createForUser($user, 'login');

        // Send notification (queued)
        $user->notify((new TwoFactorCodeNotification($twoFactorCode->code, 'login'))->onQueue('otp'));

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'A new verification code has been sent to your email.'
            ]);
        }

        return back()->with('success', 'A new verification code has been sent to your email.');
    }

    /**
     * Enable 2FA for the authenticated user
     */
    public function enable(Request $request)
    {
        $user = $request->user();

        if ($user->two_factor_enabled) {
            return back()->with('info', 'Two-factor authentication is already enabled.');
        }

        // Generate and send verification code
        $twoFactorCode = TwoFactorCode::createForUser($user, 'enable_2fa');
        $user->notify((new TwoFactorCodeNotification($twoFactorCode->code, 'login'))->onQueue('otp'));

        return back()->with('success', 'A verification code has been sent to your email. Please verify to enable 2FA.');
    }

    /**
     * Confirm enabling 2FA
     */
    public function confirmEnable(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = $request->user();

        if (!TwoFactorCode::verify($user, $request->code, 'enable_2fa')) {
            throw ValidationException::withMessages([
                'code' => 'The verification code is invalid or has expired.',
            ]);
        }

        $user->update(['two_factor_enabled' => true]);

        return back()->with('success', 'Two-factor authentication has been enabled successfully!');
    }

    /**
     * Disable 2FA for the authenticated user
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        $user->update([
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
        ]);

        return back()->with('success', 'Two-factor authentication has been disabled.');
    }

    /**
     * Automatically verify via signed link (no reliance on provisional session).
     */
    public function auto(Request $request, $id, $code)
    {
        $user = User::find($id);
        if (! $user) {
            return redirect()->route('login')->with('error', 'Invalid verification link.');
        }

        if (! TwoFactorCode::verify($user, $code, 'login')) {
            return redirect()->route('login')->with('error', 'The verification link is invalid or expired.');
        }

        // Complete login
        $this->completeLogin($request, $user);

    return redirect()->intended($this->targetUrlFor($user))->with('success', 'Login verified successfully.');
    }

    /**
     * Shared logic to finalize login after successful 2FA.
     */
    protected function completeLogin(Request $request, User $user): void
    {
        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }
        if (! $user->two_factor_enabled) {
            $user->update(['two_factor_enabled' => true]);
        }

        Auth::login($user, session('2fa:remember', false));
        session()->forget('2fa:user:id');
        session()->forget('2fa:remember');

        $request->session()->regenerate();
        $user->updateLastLogin($request->ip());

        \Log::info('2FA completeLogin executed', [
            'user_id' => $user->id,
            'authenticated' => auth()->check(),
        ]);
    }

    /**
     * Decide target URL based on role, using named routes but relative urls to avoid APP_URL issues.
     */
    protected function targetUrlFor(User $user): string
    {
        if ($user->isSuperAdmin()) {
            return route('superadmin.dashboard', absolute: false);
        }
        if ($user->isAdmin()) {
            return route('admin.dashboard', absolute: false);
        }
    // Default for customers/users: send to public welcome page
    // Add a session flag so SweetAlert appears
    session()->flash('just_logged_in', true);
    return route('welcome', absolute: false);
    }
}
