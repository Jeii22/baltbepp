public function verify(Request $request) {
    $request->validate([
        'code' => 'required|string',
    ]);

    $user = User::where('id', session('2fa:user:id'))->first();

    if ($user && Hash::check($request->code, $user->two_factor_code)) {
        Auth::login($user);
        session()->forget('2fa:user:id');
        return redirect()->intended('/home'); // Redirect to intended route
    }

    return back()->withErrors(['code' => 'The provided verification code is invalid.']);
}