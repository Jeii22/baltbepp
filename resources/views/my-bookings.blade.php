<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>My Bookings</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-white text-gray-800">

    <!-- Navbar -->
    <nav class="absolute top-0 left-0 w-full z-20 bg-black/30 backdrop-blur-sm" x-data="{ open: false }">
        <div class="max-w-7xl mx-auto flex justify-between items-center py-4 px-6">
            <!-- Logo -->
            <a href="/" class="flex items-center space-x-2">
                <img src="{{ asset('images/baltbep-logo.png') }}" class="h-20" alt="BaltBep Logo">
            </a>
            <!-- Mobile Menu Button -->
            <button @click="open = !open" class="md:hidden text-white p-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <!-- Nav Links -->
            <div class="hidden md:flex space-x-8 text-white font-medium" x-show="open || window.innerWidth >= 768" :class="{ 'flex flex-col space-y-4 mt-4': open && window.innerWidth < 768 }">
                <a href="#book" class="px-3 py-2 rounded-lg hover:bg-white/20 hover:text-cyan-200 transition-all duration-200 smooth-scroll">Book</a>
                <a href="#promos" class="px-3 py-2 rounded-lg hover:bg-white/20 hover:text-cyan-200 transition-all duration-200 smooth-scroll">Promos</a>
                <a href="#routes" class="px-3 py-2 rounded-lg hover:bg-white/20 hover:text-cyan-200 transition-all duration-200 smooth-scroll">Routes</a>
                <a href="#why-choose-us" class="px-3 py-2 rounded-lg hover:bg-white/20 hover:text-cyan-200 transition-all duration-200 smooth-scroll">Why Choose Us</a>
                <a href="#about-us" class="px-3 py-2 rounded-lg hover:bg-white/20 hover:text-cyan-200 transition-all duration-200 smooth-scroll">About Us</a>
                <a href="#contact-us" class="px-3 py-2 rounded-lg hover:bg-white/20 hover:text-cyan-200 transition-all duration-200 smooth-scroll">Contact Us</a>
            </div>
            <!-- Auth area: show user name if logged in, otherwise Sign In -->
            <div x-data="{ dropdownOpen: false }" class="relative">
                @auth
                    <button @click="dropdownOpen = !dropdownOpen" class="flex items-center space-x-2 text-white hover:text-cyan-200 transition">
                        <span>Welcome {{ Auth::user()->name }}</span>
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="dropdownOpen" @click.away="dropdownOpen = false" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg z-50">
                        <!-- My Profile -->
                        <a href="{{ route('customer.dashboard') }}" class="block px-4 py-2 text-gray-800 hover:bg-blue-50 rounded-t-lg">
                            My Profile
                        </a>
                        <!-- My Bookings -->
                        <a href="{{ route('bookings.index') }}" class="block px-4 py-2 text-gray-800 hover:bg-blue-50">
                            My Bookings
                        </a>
                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}" class="border-t">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-gray-800 hover:bg-blue-50 rounded-b-lg">
                                Logout
                            </button>
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
    

    <!-- Hero Section -->
    <div class="relative bg-cover bg-center h-[40vh]" style="background-image: url('/images/barko.png');">
        <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
            <div class="text-center text-white px-6">
                <h1 class="text-4xl md:text-5xl font-bold">Your Bookings</h1>
                <p class="mt-2 text-2xl italic">Manage and review your trips with ease.</p>
            </div>
        </div>
    </div>

    <!-- Content Section -->
    <div class="max-w-6xl mx-auto py-12">
        <header class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">My Bookings</h1>
            <p class="text-gray-600">View and manage your recent bookings.</p>
        </header>

        <section class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-lg font-medium text-gray-900">Recent Bookings</h2>
            <p class="mt-1 text-sm text-gray-600">Here are your most recent bookings:</p>

            <div class="mt-6 space-y-4">
                @forelse($bookings as $booking)
                    <div class="bg-gray-50 p-4 rounded-lg shadow">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Booking #{{ $booking->id }}</h3>
                                <p class="text-sm text-gray-600">{{ $booking->origin }} → {{ $booking->destination }}</p>
                                <p class="text-sm text-gray-500">{{ $booking->trip_date }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-semibold text-gray-900">₱{{ number_format($booking->total_amount, 2) }}</p>
                                <span class="px-3 py-1 text-xs font-semibold rounded-full
                                    @if($booking->status === 'confirmed') bg-green-100 text-green-800
                                    @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500">No bookings found.</p>
                @endforelse
            </div>
        </section>
    </div>

</body>
</html>