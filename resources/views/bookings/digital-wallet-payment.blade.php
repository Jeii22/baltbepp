<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Digital Wallet Payment - Balt-Bep</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-white text-gray-800">

    <!-- Navbar -->
    <nav class="absolute top-0 left-0 w-full z-20 bg-transparent">
        <div class="max-w-7xl mx-auto flex justify-between items-center py-4 px-6">
            <a href="/" class="flex items-center space-x-2">
                <img src="{{ asset('images/baltbep-logo.png') }}" class="h-20" alt="BaltBep Logo">
            </a>
            <div class="hidden md:flex space-x-8 text-white font-medium">
                <a href="{{ route('welcome') }}#book" class="hover:text-cyan-200">Book</a>
                <a href="{{ route('welcome') }}#promos" class="hover:text-cyan-200">Promos</a>
                <a href="{{ route('welcome') }}#routes" class="hover:text-cyan-200">Routes</a>
                <a href="{{ route('welcome') }}#why-choose-us" class="hover:text-cyan-200">Why Choose Us</a>
                <a href="{{ route('welcome') }}#about-us" class="hover:text-cyan-200">About Us</a>
                <a href="{{ route('welcome') }}#contact-us" class="hover:text-cyan-200">Contact Us</a>
            </div>
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

    <!-- Hero Section -->
    <div class="relative bg-cover bg-center h-[45vh] md:h-[55vh]" style="background-image: url('/images/barko.png');">
        <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
            <div class="text-center text-white px-6">
                <h1 class="text-3xl md:text-5xl font-bold">Complete Your Payment</h1>
                <p class="mt-2 text-lg md:text-2xl italic">{{ strtoupper($paymentMethod) }} Payment Instructions</p>
            </div>
        </div>
    </div>

    <!-- Content Card -->
    <div class="relative -mt-16 max-w-4xl mx-auto bg-white/90 backdrop-blur-md rounded-2xl shadow-2xl ring-1 ring-black/5 p-6 md:p-8">
        
        <div class="grid lg:grid-cols-2 gap-8">
            <!-- Payment Instructions -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-12 h-12 {{ $paymentMethod==='gcash' ? 'bg-blue-600' : ($paymentMethod==='paymaya' ? 'bg-green-600' : 'bg-purple-600') }} rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-sm">{{ strtoupper(substr($paymentMethod,0,2)) }}</span>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">{{ strtoupper($paymentMethod) }} Payment</h2>
                        <p class="text-sm text-gray-600">Manual confirmation required</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h3 class="font-semibold text-blue-900 mb-2">Payment Details</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-blue-700">Account Name:</span>
                                <span class="font-medium text-blue-900">{{ $wallet->account_name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-blue-700">Account Number:</span>
                                <span class="font-medium text-blue-900">{{ $wallet->account_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-blue-700">Amount to Pay:</span>
                                <span class="font-bold text-blue-900">₱{{ number_format($booking->total_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-blue-700">Reference:</span>
                                <span class="font-mono text-blue-900">{{ $paymentReference }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <h3 class="font-semibold text-yellow-900 mb-2">Payment Instructions</h3>
                        <ol class="list-decimal list-inside space-y-1 text-sm text-yellow-800">
                            <li>Scan the QR code or send money to the account details above</li>
                            <li>Use the reference number: <strong>{{ $paymentReference }}</strong></li>
                            <li>Take a screenshot of your payment confirmation</li>
                            <li>Wait for our staff to confirm your payment</li>
                            <li>You will receive a confirmation email once approved</li>
                        </ol>
                    </div>

                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center space-x-2 mb-2">
                            <div class="w-4 h-4 bg-green-500 rounded-full animate-pulse"></div>
                            <h3 class="font-semibold text-green-900">Payment Status</h3>
                        </div>
                        <p class="text-sm text-green-800" id="payment-status">
                            Waiting for payment confirmation...
                        </p>
                        <div class="mt-3">
                            <button onclick="checkPaymentStatus()" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 transition">
                                Check Status
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- QR Code -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 text-center">Scan QR Code to Pay</h3>
                
                @if($wallet->qr_code_image)
                    <div class="flex justify-center mb-4">
                        <div class="bg-white p-4 rounded-lg shadow-inner border-2 border-gray-100">
                            <img src="{{ asset('storage/' . $wallet->qr_code_image) }}" 
                                 alt="{{ strtoupper($paymentMethod) }} QR Code" 
                                 class="w-64 h-64 object-contain">
                        </div>
                    </div>
                @else
                    <div class="flex justify-center mb-4">
                        <div class="w-64 h-64 bg-gray-100 rounded-lg flex items-center justify-center">
                            <div class="text-center text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"></path>
                                </svg>
                                <p class="text-sm">QR Code not available</p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="text-center text-sm text-gray-600">
                    <p class="mb-2">Or manually send to:</p>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="font-semibold">{{ $wallet->account_name }}</p>
                        <p class="font-mono">{{ $wallet->account_number }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Summary -->
        <div class="mt-8 bg-gray-50 border border-gray-200 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Booking Summary</h3>
            <div class="grid md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p><span class="text-gray-600">Booking ID:</span> <span class="font-mono">{{ $booking->id }}</span></p>
                    <p><span class="text-gray-600">Route:</span> {{ $booking->origin }} → {{ $booking->destination }}</p>
                    <p><span class="text-gray-600">Departure:</span> {{ \Carbon\Carbon::parse($booking->departure_time)->format('M j, Y g:i A') }}</p>
                </div>
                <div>
                    <p><span class="text-gray-600">Passenger:</span> {{ $booking->full_name }}</p>
                    <p><span class="text-gray-600">Total Amount:</span> <span class="font-bold">₱{{ number_format($booking->total_amount, 2) }}</span></p>
                    <p><span class="text-gray-600">Payment Method:</span> {{ strtoupper($paymentMethod) }}</p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-6 flex flex-col sm:flex-row gap-4 justify-center">
            <button onclick="window.print()" class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition">
                Print Instructions
            </button>
            <a href="{{ route('bookings.confirmation', $booking) }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition text-center">
                View Booking Details
            </a>
        </div>
    </div>

    <script>
        let statusCheckInterval;

        function checkPaymentStatus() {
            fetch(`/booking-status/{{ $booking->id }}`)
                .then(response => response.json())
                .then(data => {
                    const statusElement = document.getElementById('payment-status');
                    
                    if (data.status === 'confirmed') {
                        statusElement.innerHTML = '<span class="text-green-700 font-semibold">✓ Payment Confirmed!</span>';
                        clearInterval(statusCheckInterval);
                        
                        // Show success message and redirect
                        setTimeout(() => {
                            window.location.href = '{{ route("bookings.confirmation", $booking) }}';
                        }, 2000);
                    } else if (data.status === 'cancelled') {
                        statusElement.innerHTML = '<span class="text-red-700 font-semibold">✗ Payment Cancelled</span>';
                        clearInterval(statusCheckInterval);
                    } else {
                        statusElement.innerHTML = 'Waiting for payment confirmation...';
                    }
                })
                .catch(error => {
                    console.error('Error checking status:', error);
                });
        }

        // Auto-check status every 30 seconds
        statusCheckInterval = setInterval(checkPaymentStatus, 30000);

        // Check status immediately when page loads
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(checkPaymentStatus, 2000);
        });
    </script>

</body>
</html>