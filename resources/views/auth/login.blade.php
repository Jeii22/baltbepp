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
        /* Ensure the official reCAPTCHA badge is visible at bottom-right */
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
            @if(config('services.recaptcha.version', 'v3') === 'v2')
                // v2 invisible: badge appears bottom-right; callback will submit
                if (typeof grecaptcha !== 'undefined') {
                    grecaptcha.execute();
                } else {
                    e.target.submit();
                }
            @else
                // v3: execute to get score token, then submit
                grecaptcha.ready(function() {
                    grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', {action: 'login'}).then(function(token) {
                        document.getElementById('recaptcha_token').value = token;
                        e.target.submit();
                    });
                });
            @endif
        }

        // reCAPTCHA v2 callback receives token
        function onRecaptchaV2Login(token) {
            document.getElementById('recaptcha_token').value = token;
            var form = document.querySelector('form[action="{{ route('login') }}"]');
            if (form) form.submit();
        }
    </script>
</head>
<body class="min-h-screen flex items-center justify-center 
             bg-gradient-to-b from-blue-600 via-cyan-400 to-white">

    

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
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <input type="hidden" id="recaptcha_token" name="recaptcha_token">

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                    <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request.otp') }}">
                    {{ __('Forgot your password?') }}
                </a>
            </div>

            <x-input-error :messages="$errors->get('recaptcha')" class="mt-2" />

            <div class="flex items-center justify-end mt-4">
                @if(config('services.recaptcha.version', 'v3') === 'v2')
                    <x-primary-button class="ml-3 g-recaptcha"
                        data-sitekey="{{ config('services.recaptcha.site_key') }}"
                        data-callback="onRecaptchaV2Login"
                        data-size="invisible">
                        {{ __('Log in') }}
                    </x-primary-button>
                @else
                    <x-primary-button class="ml-3">
                        {{ __('Log in') }}
                    </x-primary-button>
                @endif
            </div>
    </form>
    <script>
        // Attach the reCAPTCHA v3 handler to the login form
        document.addEventListener('DOMContentLoaded', function() {
            var form = document.querySelector('form[action="{{ route('login') }}"]');
            if (form) {
                form.addEventListener('submit', onLoginSubmit);
            }
        });
    </script>

    <!-- Official reCAPTCHA badge is shown bottom-right -->

        <!-- Add a register link below the sign-in button -->
        <p class="text-center text-sm text-gray-600 mt-4">
            Don't have an account? <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-800">Register here</a>
        </p>
    </div>
</body>
</html>