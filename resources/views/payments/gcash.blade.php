<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GCash Payment - Balt-Bep</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        // Simple QR generator using Google Chart API (no external deps)
        function makeQrUrl(text) {
            const size = 240;
            return `https://chart.googleapis.com/chart?cht=qr&chs=${size}x${size}&chl=${encodeURIComponent(text)}`;
        }
        document.addEventListener('DOMContentLoaded', () => {
            const url = document.getElementById('checkoutUrl').value;
            const img = document.getElementById('qr');
            img.src = makeQrUrl(url);
            // Optional: auto-open GCash in new tab on load
            // window.open(url, '_blank');
        });
    </script>
</head>
<body class="antialiased bg-white text-gray-800">
    <!-- Navbar consistent with other pages -->
    <nav class="absolute top-0 left-0 w-full z-20 bg-transparent">
        <div class="max-w-7xl mx-auto flex justify-between items-center py-4 px-6">
            <a href="/" class="flex items-center space-x-2">
                <img src="{{ asset('images/baltbep-logo.png') }}" class="h-20" alt="BaltBep Logo">
            </a>
            <div class="hidden md:flex space-x-8 text-white font-medium">
                <a href="{{ route('welcome') }}#book" class="hover:text-cyan-200">Book</a>
            </div>
        </div>
    </nav>

    <div class="relative bg-cover bg-center h-[35vh] md:h-[45vh]" style="background-image: url('/images/barko.png');">
        <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
            <div class="text-center text-white px-6">
                <h1 class="text-3xl md:text-5xl font-bold">Pay with GCash</h1>
                <p class="mt-2 text-lg md:text-2xl italic">Booking #{{ $booking->id }}</p>
            </div>
        </div>
    </div>

    <div class="relative -mt-16 max-w-4xl mx-auto bg-white/90 backdrop-blur-md rounded-2xl shadow-2xl ring-1 ring-black/5 p-6 md:p-8">
        <input type="hidden" id="checkoutUrl" value="{{ $checkoutUrl }}">
        <div class="grid md:grid-cols-2 gap-8">
            <div class="flex flex-col items-center">
                <img id="qr" alt="GCash QR" class="w-60 h-60 rounded-lg shadow" />
                <p class="mt-3 text-sm text-gray-500 text-center">Scan this QR with your phone to continue in GCash.</p>
            </div>
            <div>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">Continue to GCash</h2>
                <p class="text-gray-600 mb-4">You can also proceed directly in this device.</p>
                <a href="{{ $checkoutUrl }}" target="_blank" rel="noopener" 
                   class="inline-flex items-center px-6 py-3 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors font-medium">
                    Continue to GCash
                </a>

                <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-900">
                    <p class="font-medium">What happens next?</p>
                    <ul class="list-disc list-inside mt-1 space-y-1">
                        <li>Authorize the payment in GCash.</li>
                        <li>You'll be redirected back here.</li>
                        <li>We confirm your booking once the payment provider notifies us.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="mt-8 flex gap-4 justify-center">
            <a href="{{ route('bookings.confirmation', $booking) }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                Return to Confirmation
            </a>
        </div>
    </div>
</body>
</html>