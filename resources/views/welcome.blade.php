<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Balt-Bep</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-white text-gray-800">

    <!-- Navbar -->
    <nav class="absolute top-0 left-0 w-full z-20 bg-transparent">
        <div class="max-w-7xl mx-auto flex justify-between items-center py-4 px-6">
            <!-- Logo -->
            <a href="/" class="flex items-center space-x-2">
                <img src="{{ asset('images/baltbep-logo.png') }}" class="h-20" alt="BaltBep Logo">
            </a>
            <!-- Nav Links -->
            <div class="hidden md:flex space-x-8 text-white font-medium">
                <a href="#book" class="hover:text-cyan-200">Book</a>
                <a href="#refund" class="hover:text-cyan-200">Refund & Rebooking</a>
                <a href="#info" class="hover:text-cyan-200">Travel Info</a>
                <a href="#updates" class="hover:text-cyan-200">Latest Updates</a>
                <a href="#contact" class="hover:text-cyan-200">Contact Us</a>
            </div>
            <!-- Sign In -->
            <div>
                <a href="{{ route('login') }}" class="border border-white px-4 py-2 rounded-lg text-white hover:bg-white hover:text-blue-600 transition">
                    Sign In
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative h-[90vh] flex items-center justify-center text-center text-white"
             style="background: url('{{ asset('images/barko.png') }}') center/cover no-repeat;">
        <div class="absolute inset-0 bg-blue-700 bg-opacity-60"></div>
        <div class="relative z-10 max-w-3xl">
            <h1 class="text-5xl font-bold mb-4">Balt-Bep Ticket</h1>
            <p class="text-lg mb-6">Smart Online Booking for Ferry Passengers</p>
            <p class="mb-6">Experience seamless ferry travel between Bantayan Island and Cadiz with our intelligent booking platform. 
               Book tickets, track schedules, and manage your journey all in one place.</p>
            <a href="#book" class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-cyan-100 transition">
                Book Your Ferry Now →
            </a>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-12 bg-white">
        <div class="max-w-6xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            <div>
                <h2 class="text-2xl font-bold text-blue-600">12+</h2>
                <p class="text-gray-600">Daily Trips</p>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-blue-600">25K+</h2>
                <p class="text-gray-600">Happy Passengers</p>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-blue-600">4.9 ⭐</h2>
                <p class="text-gray-600">Average Rating</p>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-blue-600">1.5hrs</h2>
                <p class="text-gray-600">Travel Time</p>
            </div>
        </div>
    </section>

    <!-- Why Choose BaltBep -->
    <section class="py-16 bg-gray-100">
        <div class="max-w-6xl mx-auto text-center mb-12">
            <h2 class="text-3xl font-bold text-blue-700">Why Choose BaltBep?</h2>
            <p class="text-gray-600 mt-2">Your trusted ferry booking platform with seamless features</p>
        </div>
        <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">
            <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
                <h3 class="text-xl font-bold text-blue-600 mb-2">Easy Booking</h3>
                <p class="text-gray-600">Book your tickets online in just a few clicks.</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
                <h3 class="text-xl font-bold text-blue-600 mb-2">Secure Payments</h3>
                <p class="text-gray-600">Your transactions are safe with us.</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
                <h3 class="text-xl font-bold text-blue-600 mb-2">Real-time Updates</h3>
                <p class="text-gray-600">Stay informed about trip schedules and changes.</p>
            </div>
        </div>
    </section>

</body>
</html>
