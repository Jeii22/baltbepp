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
        if (!session()->has('registration:captcha:answer')) {
            $this->setCaptcha();
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class, 'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()->min(8)->mixedCase()->numbers()->symbols()],
            'terms' => ['accepted'],
            'captcha' => ['required', function ($attribute, $value, $fail) {
                $expected = session('registration:captcha:answer');

                if (!$expected || trim((string) $value) !== $expected) {
                    $this->setCaptcha();
                    $fail('The captcha answer is incorrect.');
                }
            }],
        ], [
            'terms.accepted' => 'You must agree to the terms and conditions before continuing.',
            'email.regex' => 'Only Gmail addresses are allowed for registration.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.mixed_case' => 'Password must contain at least one uppercase and one lowercase letter.',
            'password.numbers' => 'Password must contain at least one number.',
            'password.symbols' => 'Password must contain at least one special character.',
            'captcha.required' => 'Please solve the captcha to continue.',
        ]);

        session()->forget('registration:captcha:question');
        session()->forget('registration:captcha:answer');

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        Auth::login($user);

        event(new Registered($user));

        $twoFactorCode = \App\Models\TwoFactorCode::createForUser($user, 'registration');
        $user->notify(new \App\Notifications\TwoFactorCodeNotification($twoFactorCode->code, 'registration'));

        session(['registration:user:id' => $user->id]);

        return redirect()->route('verification.notice')->with('success', 'Registration successful! Please check your Gmail for the verification code.');
    }

    protected function setCaptcha(): void
    {
        $first = random_int(1, 9);
        $second = random_int(1, 9);
        $answer = (string) ($first + $second);

        session([
            'registration:captcha:question' => 'What is '.$first.' + '.$second.'?',
            'registration:captcha:answer' => $answer,
        ]);
    }
}
