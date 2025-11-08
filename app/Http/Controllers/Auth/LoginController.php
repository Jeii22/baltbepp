use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

public function login(Request $request)
{
    $recaptchaToken = $request->input('recaptcha_token');
    $recaptchaSecret = 'YOUR_SECRET_KEY'; // Replace with your secret key

    $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
        'secret' => $recaptchaSecret,
        'response' => $recaptchaToken,
        'remoteip' => $request->ip(),
    ]);

    $result = $response->json();

    if (!($result['success'] ?? false) || ($result['score'] ?? 0) < 0.5) {
        return back()->withErrors(['recaptcha' => 'reCAPTCHA verification failed.']);
    }

    // ... proceed with your login logic ...
}