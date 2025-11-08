<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'BaltBep Ticketing') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if(config('services.recaptcha.version', 'v3') === 'v2')
        <!-- Google reCAPTCHA v2 Invisible -->
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @else
        <!-- Google reCAPTCHA v3 -->
        <script src="https://www.google.com/recaptcha/api.js?render={{ env('RECAPTCHA_PUBLIC_KEY') }}"></script>
    @endif
    <style>
        .grecaptcha-badge {
            visibility: visible !important;
            opacity: 1 !important;
            position: fixed !important;
            right: 12px !important;
            bottom: 12px !important;
            z-index: 9999 !important;
        }
    </style>
    <script>
        function onRegisterSubmit(e) {
            e.preventDefault();
            @if(config('services.recaptcha.version', 'v3') === 'v2')
                if (typeof grecaptcha !== 'undefined') {
                    grecaptcha.execute();
                } else {
                    e.target.submit();
                }
            @else
                grecaptcha.ready(function() {
                    grecaptcha.execute('{{ env('RECAPTCHA_PUBLIC_KEY') }}', {action: 'register'}).then(function(token) {
                        document.getElementById('recaptcha_token').value = token;
                        e.target.submit();
                    });
                });
            @endif
        }

        function onRecaptchaV2Register(token) {
            document.getElementById('recaptcha_token').value = token;
            var form = document.querySelector('form[action="{{ route('register') }}"]');
            if (form) form.submit();
        }
    </script>
</head>
<body class="min-h-screen flex items-center justify-center 
             bg-gradient-to-b from-blue-600 via-cyan-400 to-white">

    <!-- Card -->
    <div class="w-full max-w-md bg-white shadow-xl rounded-2xl p-8 space-y-6">
        <a href="{{ route('login') }}" class="flex items-center text-sm text-gray-500 hover:text-gray-800">
            ← Back to sign in
        </a>

        <!-- Title -->
        <h2 class="text-2xl font-bold text-center text-gray-900">
            Create your account
        </h2>

        <!-- Form -->
        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf
            <input type="hidden" id="recaptcha_token" name="recaptcha_token">

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                <div class="mt-1 relative">
              <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                  class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 pl-10"
                  placeholder="Juan Dela Cruz"
                  oninput="this.value = this.value.replace(/[^A-Za-zÀ-ÿ\s\-']/g, '')">
                    <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                        👤
                    </span>
                </div>
                @error('name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <div class="mt-1 relative">
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 pl-10"
                           placeholder="you@gmail.com">
                    <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                        📧
                    </span>
                </div>
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <div class="mt-1 relative">
                    <input id="password" type="password" name="password" required
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 pl-10"
                           placeholder="Min. 8 characters">
                    <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                        🔒
                    </span>
                </div>
                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <div class="mt-1 relative">
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 pl-10"
                           placeholder="Re-enter password">
                    <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                        🔒
                    </span>
                </div>
            </div>

            <!-- reCAPTCHA v3 token is generated automatically; show any verification errors -->
            @error('recaptcha')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror

            <div class="flex items-start space-x-3">
                <input id="terms" type="checkbox" name="terms" value="1" class="mt-1 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" {{ old('terms') ? 'checked' : '' }}>
                <label for="terms" class="text-sm text-gray-600">
                    I agree to the
                    <button type="button" id="open-tos" class="text-blue-600 hover:text-blue-800 underline">Terms of Service</button>
                    and
                    <button type="button" id="open-privacy" class="text-blue-600 hover:text-blue-800 underline">Privacy Policy</button>.
                </label>
            </div>
            @error('terms')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror

            @if(config('services.recaptcha.version', 'v3') === 'v2')
                <button type="submit"
                    class="w-full py-3 px-4 bg-gray-900 text-white font-semibold rounded-lg shadow hover:bg-gray-800 transition g-recaptcha"
                    data-sitekey="{{ config('services.recaptcha.site_key') }}"
                    data-callback="onRecaptchaV2Register"
                    data-size="invisible">
                    Create account
                </button>
            @else
                <button type="submit"
                    class="w-full py-3 px-4 bg-gray-900 text-white font-semibold rounded-lg shadow hover:bg-gray-800 transition">
                    Create account
                </button>
            @endif
        </form>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var form = document.querySelector('form[action="{{ route('register') }}"]');
                if (form) {
                    form.addEventListener('submit', onRegisterSubmit);
                }
            });
        </script>
    </div>
    <!-- Terms of Service Modal -->
    <div id="tos-modal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="relative mx-auto my-10 w-11/12 max-w-3xl bg-white rounded-lg shadow-xl overflow-hidden">
            <div class="flex items-center justify-between border-b px-4 py-3">
                <h3 class="text-lg font-semibold">Terms of Service</h3>
                <button type="button" class="p-2" id="close-tos" aria-label="Close Terms">✕</button>
            </div>
            <div class="p-4 max-h-[70vh] overflow-y-auto prose prose-sm">
                @includeIf('terms-of-service-modal', [])
                @unless (View::exists('terms-of-service-modal'))
                    <h4 class="font-semibold mb-2">Summary</h4>
                    <p>By creating an account, you agree to comply with our usage rules, provide accurate information, and acknowledge that services may change. For the full version, see the Terms page.</p>
                @endunless
            </div>
            <div class="border-t px-4 py-3 flex justify-end gap-2">
                <button type="button" id="close-tos-bottom" class="px-4 py-2 bg-gray-800 text-white rounded">Close</button>
            </div>
        </div>
    </div>

    <!-- Privacy Policy Modal -->
    <div id="privacy-modal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="relative mx-auto my-10 w-11/12 max-w-3xl bg-white rounded-lg shadow-xl overflow-hidden">
            <div class="flex items-center justify-between border-b px-4 py-3">
                <h3 class="text-lg font-semibold">Privacy Policy</h3>
                <button type="button" class="p-2" id="close-privacy" aria-label="Close Privacy">✕</button>
            </div>
            <div class="p-4 max-h-[70vh] overflow-y-auto prose prose-sm">
                @includeIf('privacy-policy-modal', [])
                @unless (View::exists('privacy-policy-modal'))
                    <h4 class="font-semibold mb-2">Summary</h4>
                    <p>We collect and process your data to provide services, secure your account, and improve features. See the full Privacy Policy page for details on retention and rights.</p>
                @endunless
            </div>
            <div class="border-t px-4 py-3 flex justify-end gap-2">
                <button type="button" id="close-privacy-bottom" class="px-4 py-2 bg-gray-800 text-white rounded">Close</button>
            </div>
        </div>
    </div>

    <script>
        (function(){
            function show(id){ document.getElementById(id).classList.remove('hidden'); }
            function hide(id){ document.getElementById(id).classList.add('hidden'); }
            document.getElementById('open-tos')?.addEventListener('click', function(){ show('tos-modal'); });
            document.getElementById('close-tos')?.addEventListener('click', function(){ hide('tos-modal'); });
            document.getElementById('close-tos-bottom')?.addEventListener('click', function(){ hide('tos-modal'); });
            document.getElementById('open-privacy')?.addEventListener('click', function(){ show('privacy-modal'); });
            document.getElementById('close-privacy')?.addEventListener('click', function(){ hide('privacy-modal'); });
            document.getElementById('close-privacy-bottom')?.addEventListener('click', function(){ hide('privacy-modal'); });
            // Close when clicking backdrop
            document.getElementById('tos-modal')?.addEventListener('click', function(e){ if(e.target === this) hide('tos-modal'); });
            document.getElementById('privacy-modal')?.addEventListener('click', function(e){ if(e.target === this) hide('privacy-modal'); });
            // ESC key
            document.addEventListener('keydown', function(e){ if(e.key === 'Escape'){ hide('tos-modal'); hide('privacy-modal'); }});
        })();
    </script>
</body>
</html>
