<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'BaltBep') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center 
                pt-6 sm:pt-0 
                bg-gradient-to-b from-white via-cyan-400 to-blue-600">

        <!-- Logo -->
        <div class="flex justify-center mb-6">
            <a href="/">
                <img src="{{ asset('images/baltbep-logo.png') }}" 
                     alt="BaltBep Logo" 
                     class="h-40 w-auto drop-shadow-lg">
            </a>
        </div>

        <!-- Card -->
        <div class="w-full sm:max-w-md mt-6 px-8 py-6 
                    bg-white 
                    shadow-xl rounded-2xl border border-gray-200">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
