<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Booking Confirmation - Balt-Bep</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-white text-gray-800">

    <!-- Navbar (same as passenger/checkout/welcome style) -->
    <nav class="absolute top-0 left-0 w-full z-20 bg-transparent">
        <div class="max-w-7xl mx-auto flex justify-between items-center py-4 px-6">
            <!-- Logo -->
            <a href="/" class="flex items-center space-x-2">
                <img src="{{ asset('images/baltbep-logo.png') }}" class="h-20" alt="BaltBep Logo">
            </a>
            <!-- Nav Links -->
            <div class="hidden md:flex space-x-8 text-white font-medium">
                <a href="{{ route('welcome') }}#book" class="hover:text-cyan-200">Book</a>
                <a href="{{ route('welcome') }}#promos" class="hover:text-cyan-200">Promos</a>
                <a href="{{ route('welcome') }}#routes" class="hover:text-cyan-200">Routes</a>
                <a href="{{ route('welcome') }}#why-choose-us" class="hover:text-cyan-200">Why Choose Us</a>
                <a href="{{ route('welcome') }}#about-us" class="hover:text-cyan-200">About Us</a>
                <a href="{{ route('welcome') }}#contact-us" class="hover:text-cyan-200">Contact Us</a>
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

    <!-- Hero Section to match previous pages -->
    <div class="relative bg-cover bg-center h-[45vh] md:h-[55vh]" style="background-image: url('/images/barko.png');">
        <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
            <div class="text-center text-white px-6">
                <h1 class="text-3xl md:text-5xl font-bold">Booking Confirmation</h1>
                <p class="mt-2 text-lg md:text-2xl italic">
                    {{ $booking->origin }} → {{ $booking->destination }}
                    @if($booking->departure_time)
                        on {{ optional($booking->departure_time)->format('M d, Y') }}
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Content Card with progress like passenger/checkout pages -->
    <div class="relative -mt-16 max-w-6xl mx-auto bg-white/90 backdrop-blur-md rounded-2xl shadow-2xl ring-1 ring-black/5 p-6 md:p-8">
        @include('bookings.partials.progress', ['current' => 'confirmation'])

        <!-- Booking Details Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Booking Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Booking Reference:</span>
                        <span class="font-semibold text-blue-600">#{{ $booking->id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Status:</span>
                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                            @if($booking->status === 'confirmed') bg-green-100 text-green-800
                            @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Route:</span>
                        <span class="font-medium">{{ $booking->origin }} → {{ $booking->destination }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Departure:</span>
                        <span class="font-medium">{{ optional($booking->departure_time)->format('M d, Y • h:i A') }}</span>
                    </div>
                </div>
                <!-- Right Column -->
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Passenger Name:</span>
                        <span class="font-medium">{{ $booking->full_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Email:</span>
                        <span class="font-medium">{{ $booking->email }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Phone:</span>
                        <span class="font-medium">{{ $booking->phone ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Passengers:</span>
                        <span class="font-medium">
                            @php
                                $passengerCounts = [];
                                if($booking->adult > 0) $passengerCounts[] = $booking->adult . ' Adult' . ($booking->adult > 1 ? 's' : '');
                                if($booking->child > 0) $passengerCounts[] = $booking->child . ' Child' . ($booking->child > 1 ? 'ren' : '');
                                if($booking->infant > 0) $passengerCounts[] = $booking->infant . ' Infant' . ($booking->infant > 1 ? 's' : '');
                                if($booking->pwd > 0) $passengerCounts[] = $booking->pwd . ' PWD/Senior' . ($booking->pwd > 1 ? 's' : '');
                            @endphp
                            {{ implode(', ', $passengerCounts) }}
                        </span>
                    </div>
                </div>
            </div>
            <!-- Total Amount -->
            <div class="border-t border-gray-200 mt-6 pt-4">
                <div class="flex justify-between items-center">
                    <span class="text-lg font-semibold text-gray-900">Total Amount:</span>
                    <span class="text-2xl font-bold text-blue-600">₱{{ number_format($booking->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Important Information -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8">
            <h3 class="font-semibold text-blue-900 mb-2">Important Information</h3>
            <ul class="text-sm text-blue-800 space-y-1">
                <li>• Please arrive at the terminal at least 30 minutes before departure</li>
                <li>• Bring a valid ID for verification</li>
                @if($booking->status === 'confirmed' && str_contains(strtolower(session('success', '')), 'cash'))
                    <li>• <strong>Payment Method:</strong> Cash on Departure - Please pay at the terminal</li>
                @endif
                <li>• Keep this confirmation for your records</li>
                <li>• For changes or cancellations, contact our customer service</li>
            </ul>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('welcome') }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium text-center">
                Book Another Trip
            </a>
            <button onclick="window.print()" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                Print Confirmation
            </button>
        </div>
    </div>
</body>
</html>