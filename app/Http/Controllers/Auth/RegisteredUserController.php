<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        if (!session()->has('registration:captcha:question')) {
            $this->setCaptcha();
        }

        return view('auth.register', [
            'captchaQuestion' => session('registration:captcha:question'),
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                // Only allow letters, spaces, hyphens, and apostrophes (no numbers or special chars)
                'regex:/^[A-Za-zÀ-ÿ\s\-\']+$/u',
            ],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class, 'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()->min(8)->mixedCase()->numbers()->symbols()],
            'terms' => ['accepted'],
            'recaptcha_token' => ['required','string'],
        ], [
            'name.regex' => 'Full name may only contain letters, spaces, hyphens, and apostrophes.',
            'terms.accepted' => 'You must agree to the terms and conditions before continuing.',
            'email.regex' => 'Only Gmail addresses are allowed for registration.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.mixed_case' => 'Password must contain at least one uppercase and one lowercase letter.',
            'password.numbers' => 'Password must contain at least one number.',
            'password.symbols' => 'Password must contain at least one special character.',
            'recaptcha_token.required' => 'reCAPTCHA verification failed. Please retry.',
        ]);

        // Verify reCAPTCHA v3 via centralized service
        try {
            app(\App\Services\RecaptchaService::class)->assertValid($request->input('recaptcha_token'), 'register');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        Auth::login($user);

        event(new Registered($user));

        // Generate OTP
        $twoFactorCode = \App\Models\TwoFactorCode::createForUser($user, 'registration');

        // Send OTP to the user's email
        $user->notify(new \App\Notifications\TwoFactorCodeNotification($twoFactorCode->code, 'registration'));

        // Store user ID in session for verification
        session(['registration:user:id' => $user->id]);

        return redirect()->route('verification.notice')->with('success', 'Registration successful! Please check your Gmail for the verification code.');
    }

    protected function setCaptcha(): void
    {
    // Math captcha removed; reCAPTCHA v3 now sole bot mitigation.
    }
}
