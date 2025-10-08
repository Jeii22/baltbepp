<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Balt-Bep</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="antialiased bg-white text-gray-800">

    <!-- Navbar (same as welcome/search) -->
    <nav class="absolute top-0 left-0 w-full z-20 bg-transparent" x-data="{ open: false }">
        <div class="max-w-7xl mx-auto flex justify-between items-center py-4 px-6">
            <!-- Logo -->
            <a href="/" class="flex items-center space-x-2">
                <img src="{{ asset('images/baltbep-logo.png') }}" class="h-20" alt="BaltBep Logo">
            </a>

            <!-- Mobile menu button -->
            <button @click="open = !open" class="md:hidden text-white hover:text-cyan-200 focus:outline-none">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <!-- Nav Links -->
            <div class="hidden md:flex space-x-8 text-white font-medium">
                <a href="{{ route('welcome') }}#book" class="hover:text-cyan-200">Book</a>
                <a href="{{ route('welcome') }}#promos" class="hover:text-cyan-200">Promos</a>
                <a href="{{ route('welcome') }}#routes" class="hover:text-cyan-200">Routes</a>
                <a href="{{ route('welcome') }}#why-choose-us" class="hover:text-cyan-200">Why Choose Us</a>
                <a href="{{ route('welcome') }}#about-us" class="hover:text-cyan-200">About Us</a>
                <a href="{{ route('welcome') }}#contact-us" class="hover:text-cyan-200">Contact Us</a>
            </div>
            <!-- Mobile Nav Links -->
            <div x-show="open" class="md:hidden absolute top-full left-0 w-full bg-black bg-opacity-80 text-white py-4 px-6 space-y-2">
                <a href="{{ route('welcome') }}#book" class="block hover:text-cyan-200">Book</a>
                <a href="{{ route('welcome') }}#promos" class="block hover:text-cyan-200">Promos</a>
                <a href="{{ route('welcome') }}#routes" class="block hover:text-cyan-200">Routes</a>
                <a href="{{ route('welcome') }}#why-choose-us" class="block hover:text-cyan-200">Why Choose Us</a>
                <a href="{{ route('welcome') }}#about-us" class="block hover:text-cyan-200">About Us</a>
                <a href="{{ route('welcome') }}#contact-us" class="block hover:text-cyan-200">Contact Us</a>
            </div>
            <!-- Auth area -->
            <div>
                @auth
                    <div class="flex items-center space-x-3 text-white">
                        <span>Hi, {{ Auth::user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="border border-white px-3 py-1 rounded-lg hover:bg-white hover:text-blue-600 transition">Log out</button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="border border-white px-4 py-2 rounded-lg text-white hover:bg-white hover:text-blue-600 transition">
                        Sign In
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero Section (same background style) -->
    <div class="relative bg-cover bg-center h-[45vh] md:h-[55vh]" style="background-image: url('/images/barko.png');">
        <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
            <div class="text-center text-white px-6">
                <h1 class="text-3xl md:text-5xl font-bold">Passenger Details</h1>
                <p class="mt-2 text-lg md:text-2xl italic">
                    {{ $trip->origin }} → {{ $trip->destination }} ·
                    Depart: {{ \Carbon\Carbon::parse($trip->departure_time)->format('M d, Y h:i A') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Content Card (mirrors Trip Search Box style) -->
    <div class="relative -mt-16 max-w-6xl mx-auto bg-white/90 backdrop-blur-md rounded-2xl shadow-2xl ring-1 ring-black/5 p-6 md:p-8">
        @include('bookings.partials.progress', ['current' => 'passenger'])

        <form action="{{ route('bookings.summary') }}" method="POST" class="mt-4 space-y-6">
            @csrf
            <input type="hidden" name="trip_id" value="{{ $trip->id }}">

            <!-- Passenger counts -->
            <section>
                <h2 class="text-lg font-semibold text-gray-800">Passengers</h2>
                <p class="text-sm text-gray-500 mb-4">Enter the number of passengers by type.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="text-sm font-semibold text-gray-700">Adult</label>
                        <input type="number" name="adult" value="{{ request('adult', 1) }}" min="1" class="mt-1 block w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-700">Child (2-11)</label>
                        <input type="number" name="child" value="{{ request('child', 0) }}" min="0" class="mt-1 block w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-700">Infant</label>
                        <input type="number" name="infant" value="{{ request('infant', 0) }}" min="0" class="mt-1 block w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-700">PWD / Senior</label>
                        <input type="number" name="pwd" value="{{ request('pwd', 0) }}" min="0" class="mt-1 block w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </section>

            <!-- Primary contact -->
            <section>
                <h2 class="text-lg font-semibold text-gray-800">Primary Contact</h2>
                <p class="text-sm text-gray-500 mb-4">We’ll send booking details and updates here.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold text-gray-700">Full Name</label>
                        <input type="text" name="full_name" required placeholder="Juan Dela Cruz" class="mt-1 block w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-700">Email</label>
                        <input type="email" name="email" required placeholder="you@example.com" class="mt-1 block w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-sm font-semibold text-gray-700">Phone (optional)</label>
                        <input type="text" name="phone" placeholder="0917 123 4567" class="mt-1 block w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </section>

            <!-- Actions -->
            <div class="flex items-center justify-between">
                <a href="{{ url()->previous() }}" class="inline-flex items-center px-6 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">Back</a>
                <div class="text-right">
                    <button type="submit" class="bg-green-600 text-white font-medium rounded-lg px-6 py-3 hover:bg-green-700 active:bg-green-800 transition shadow">
                        Proceed
                    </button>
                    <p class="mt-2 text-xs text-gray-400">By continuing, you agree to our terms.</p>
                </div>
            </div>
        </form>
    </div>
</body>
</html>