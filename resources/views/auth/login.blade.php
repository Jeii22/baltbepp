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
        <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
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
        function onLoginSubmit(e) {
            e.preventDefault();
            showLoading('Signing in...');
            @if(config('services.recaptcha.version', 'v3') === 'v2')
                if (typeof grecaptcha !== 'undefined') {
                    grecaptcha.execute();
                } else {
                    e.target.submit();
                }
            @else
                grecaptcha.ready(function() {
                    grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', {action: 'login'}).then(function(token) {
                        document.getElementById('recaptcha_token').value = token;
                        e.target.submit();
                    });
                });
            @endif
        }
        function onRecaptchaV2Login(token) {
            document.getElementById('recaptcha_token').value = token;
            var form = document.querySelector('form[action="{{ route('login') }}"]');
            if (form) form.submit();
        }
    </script>
</head>
<body class="min-h-screen flex items-center justify-center 
             bg-gradient-to-b from-blue-600 via-cyan-400 to-white">

    <x-loading-screen message="Signing in..." />

    <!-- Card -->
    <div class="w-full max-w-md bg-white shadow-xl rounded-2xl p-8 space-y-6">
        <!-- Move the logo above the card -->
    <div class="flex justify-center mb-6">
        <a href="{{ url('/') }}">
            <img src="{{ asset('images/baltbep-logo.png') }}" alt="BaltBep Logo" class="h-16">
        </a>
    </div>
        <!-- Title -->
        <h2 class="text-2xl font-bold text-center text-gray-900">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-2">Welcome to Balt-Bep Ferries</h2>
        </h2>

        <!-- Form -->
        <form method="POST" action="{{ route('login') }}" class="space-y-5" novalidate>
            @csrf
            <input type="hidden" id="recaptcha_token" name="recaptcha_token">

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <div class="mt-1 relative">
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 pl-10"
                           placeholder="you@gmail.com">
                    <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                        
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
                           placeholder="Your password">
                    <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                        
                    </span>
                </div>
                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <label for="remember_me" class="flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring focus:ring-blue-200" name="remember">
                    <span class="ml-2 text-sm text-gray-600">Remember me</span>
                </label>

                <a href="{{ route('password.request.otp') }}" class="text-sm text-blue-600 hover:text-blue-800">Forgot password?</a>
            </div>

            @if(config('services.recaptcha.version', 'v3') === 'v2')
                <button type="submit"
                        class="w-full py-3 px-4 bg-gray-900 text-white font-semibold rounded-lg shadow hover:bg-gray-800 transition g-recaptcha"
                        data-sitekey="{{ config('services.recaptcha.site_key') }}"
                        data-callback="onRecaptchaV2Login"
                        data-size="invisible">
                    Sign in
                </button>
            @else
                <button type="submit"
                        class="w-full py-3 px-4 bg-gray-900 text-white font-semibold rounded-lg shadow hover:bg-gray-800 transition">
                    Sign in
                </button>
            @endif
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var form = document.querySelector('form[action="{{ route('login') }}"]');
            if (form) form.addEventListener('submit', onLoginSubmit);
        });
    </script>
        </form>

        <!-- Add a register link below the sign-in button -->
        <p class="text-center text-sm text-gray-600 mt-4">
            Don't have an account? <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-800">Register here</a>
        </p>

        <!-- Back to Home button -->
        <div class="text-center mt-4">
            <a href="{{ url('/') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 transition">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Home
            </a>
        </div>
    </div>
</body>
</html>
