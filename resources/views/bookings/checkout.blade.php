<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Payment - Balt-Bep</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-white text-gray-800">

    <!-- Navbar (aligned with passenger/summary pages) -->
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

    <!-- Hero Section (same style) -->
    <div class="relative bg-cover bg-center h-[45vh] md:h-[55vh]" style="background-image: url('/images/barko.png');">
        <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
            <div class="text-center text-white px-6">
                <h1 class="text-3xl md:text-5xl font-bold">Payment</h1>
                <p class="mt-2 text-lg md:text-2xl italic">Complete your booking</p>
            </div>
        </div>
    </div>

    <!-- Content Card (matches previous pages) -->
    <div class="relative -mt-16 max-w-6xl mx-auto bg-white/90 backdrop-blur-md rounded-2xl shadow-2xl ring-1 ring-black/5 p-6 md:p-8">
        @include('bookings.partials.progress', ['current' => 'payment'])

        <div class="grid lg:grid-cols-3 gap-8 mt-6">
            <!-- Payment Form -->
            <div class="lg:col-span-2">
                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                    <h1 class="text-2xl font-bold text-gray-900 mb-6">Complete Your Payment</h1>

                    <!-- Error Messages -->
                    @if($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-center space-x-2 mb-2">
                                <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <h4 class="font-medium text-red-800">Please fix the following errors:</h4>
                            </div>
                            <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('bookings.process') }}" method="POST" id="paymentForm" class="space-y-6">
                        @csrf
                        
                        <!-- Payment Method Selection -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Choose Payment Method</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                
                                <!-- Digital Wallets -->
                                <div class="space-y-3">
                                    <h4 class="text-sm font-medium text-gray-700 uppercase tracking-wide">Digital Wallets</h4>

                                    @if(isset($wallets) && count($wallets))
                                        <div class="space-y-3">
                                            @foreach($wallets as $wallet)
                                            <label class="payment-option relative flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-300 transition-colors">
                                                <input type="radio" name="payment_method" value="{{ $wallet->type }}" class="sr-only" {{ old('payment_method', 'cod') == $wallet->type ? 'checked' : '' }}>
                                                <div class="flex items-center space-x-3 w-full">
                                                    <div class="w-12 h-12 {{ $wallet->type==='gcash' ? 'bg-blue-600' : 'bg-green-600' }} rounded-lg flex items-center justify-center">
                                                        <span class="text-white font-bold text-sm">{{ strtoupper(substr($wallet->type,0,2)) }}</span>
                                                    </div>
                                                    <div class="flex-1">
                                                        <p class="font-medium text-gray-900">{{ strtoupper($wallet->type) }} — {{ $wallet->label }}</p>
                                                        <p class="text-xs text-gray-500">Account: {{ $wallet->account_name }} ({{ $wallet->account_number }})</p>

                                                        @if($wallet->type==='paymaya')
                                                        <div class="mt-3 hidden paymaya-extra">
                                                            <label class="block text-xs text-gray-600 mb-1">Your Maya Phone</label>
                                                            <input type="text" name="paymaya_phone" placeholder="09xxxxxxxxx" value="{{ old('paymaya_phone') }}" class="w-full px-3 py-2 border @error('paymaya_phone') border-red-300 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                            @error('paymaya_phone')
                                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                            @enderror
                                                        </div>
                                                        @endif
                                                    </div>
                                                    <div class="payment-check hidden">
                                                        <div class="w-5 h-5 bg-blue-600 rounded-full flex items-center justify-center">
                                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-500">No admin-configured wallets available.</p>
                                    @endif
                                </div>

                                <!-- Cards & Others -->
                                <div class="space-y-3">
                                    <h4 class="text-sm font-medium text-gray-700 uppercase tracking-wide">Cards & Others</h4>
                                    
                                    <!-- Credit/Debit Card -->
                                    <label class="payment-option relative flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-300 transition-colors">
                                        <input type="radio" name="payment_method" value="card" class="sr-only" {{ old('payment_method') == 'card' ? 'checked' : '' }}>
                                        <div class="flex items-center space-x-3 w-full">
                                            <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v2H4V6zm0 4h12v4H4v-4z"></path>
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <p class="font-medium text-gray-900">Credit/Debit Card</p>
                                                <p class="text-sm text-gray-500">Visa, Mastercard, JCB</p>
                                            </div>
                                            <div class="payment-check hidden">
                                                <div class="w-5 h-5 bg-blue-600 rounded-full flex items-center justify-center">
                                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </label>

                                    @if(!empty($paymongoEnabled))
                                    <!-- PayMongo GCash (hosted) -->
                                    <label class="payment-option relative flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-300 transition-colors">
                                        <input type="radio" name="payment_method" value="paymongo_gcash" class="sr-only" {{ old('payment_method') == 'paymongo_gcash' ? 'checked' : '' }}>
                                        <div class="flex items-center space-x-3 w-full">
                                            <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                                                <span class="text-white font-bold text-sm">GC</span>
                                            </div>
                                            <div class="flex-1">
                                                <p class="font-medium text-gray-900">GCash (Hosted via PayMongo)</p>
                                                <p class="text-xs text-gray-500">Redirect to GCash to complete your payment</p>
                                            </div>
                                            <div class="payment-check hidden">
                                                <div class="w-5 h-5 bg-blue-600 rounded-full flex items-center justify-center">
                                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                    @endif

                                    <!-- Cash on Departure -->
                                    <label class="payment-option relative flex items-center p-4 border-2 border-blue-300 rounded-lg cursor-pointer bg-blue-50">
                                        <input type="radio" name="payment_method" value="cod" class="sr-only" {{ old('payment_method', 'cod') == 'cod' ? 'checked' : '' }}>
                                        <div class="flex items-center space-x-3 w-full">
                                            <div class="w-12 h-12 bg-orange-600 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <p class="font-medium text-gray-900">Cash on Departure</p>
                                                <p class="text-sm text-gray-500">Pay at the terminal</p>
                                            </div>
                                            <div class="payment-check">
                                                <div class="w-5 h-5 bg-blue-600 rounded-full flex items-center justify-center">
                                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Card Details (Hidden by default) -->
                        <div id="cardDetails" class="hidden space-y-4 p-4 bg-gray-50 rounded-lg">
                            <h4 class="font-medium text-gray-900">Card Information</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Card Number</label>
                                    <input type="text" name="card_number" placeholder="1234 5678 9012 3456" value="{{ old('card_number') }}"
                                           class="w-full px-3 py-2 border @error('card_number') border-red-300 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    @error('card_number')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                                    <input type="text" name="card_expiry" placeholder="MM/YY" value="{{ old('card_expiry') }}"
                                           class="w-full px-3 py-2 border @error('card_expiry') border-red-300 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    @error('card_expiry')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">CVV</label>
                                    <input type="text" name="card_cvv" placeholder="123" value="{{ old('card_cvv') }}"
                                           class="w-full px-3 py-2 border @error('card_cvv') border-red-300 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    @error('card_cvv')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Cardholder Name</label>
                                    <input type="text" name="card_name" placeholder="John Doe" value="{{ old('card_name') }}"
                                           class="w-full px-3 py-2 border @error('card_name') border-red-300 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    @error('card_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="flex items-start space-x-3">
                            <input type="checkbox" id="terms" name="terms" required 
                                   class="mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="terms" class="text-sm text-gray-600">
                                I agree to the <a href="#" class="text-blue-600 hover:underline">Terms and Conditions</a> 
                                and <a href="#" class="text-blue-600 hover:underline">Privacy Policy</a>
                            </label>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4 pt-6">
                            <a href="{{ url()->previous() }}" 
                               class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-center font-medium">
                                ← Back to Summary
                            </a>
                            <button type="submit" 
                                    class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center justify-center space-x-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span>Complete Payment</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h2>
                    
                    <!-- Trip Details -->
                    <div class="border-b border-gray-200 pb-4 mb-4">
                        <div class="flex items-center space-x-3 mb-2">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $outboundTrip->origin }} → {{ $outboundTrip->destination }}</p>
                                <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($outboundTrip->departure_time)->format('M d, Y • h:i A') }}</p>
                            </div>
                        </div>
                        @if(isset($inboundTrip))
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $inboundTrip->destination }} → {{ $inboundTrip->origin }}</p>
                                <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($inboundTrip->departure_time)->format('M d, Y • h:i A') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Passenger Breakdown -->
                    <div class="space-y-3 mb-4">
                        @if($counts['adult'] > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Adult × {{ $counts['adult'] }}</span>
                            <span class="font-medium">₱{{ number_format($counts['adult'] * ($fares['adult'] ?? 100), 2) }}</span>
                        </div>
                        @endif
                        @if($counts['child'] > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Child × {{ $counts['child'] }}</span>
                            <span class="font-medium">₱{{ number_format($counts['child'] * ($fares['child'] ?? 80), 2) }}</span>
                        </div>
                        @endif
                        @if($counts['infant'] > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Infant × {{ $counts['infant'] }}</span>
                            <span class="font-medium">₱{{ number_format($counts['infant'] * ($fares['infant'] ?? 0), 2) }}</span>
                        </div>
                        @endif
                        @if($counts['pwd'] > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">PWD/Senior × {{ $counts['pwd'] }}</span>
                            <span class="font-medium">₱{{ number_format($counts['pwd'] * ($fares['pwd'] ?? 80), 2) }}</span>
                        </div>
                        @endif
                        @if($counts['student'] > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Student × {{ $counts['student'] }}</span>
                            <span class="font-medium">₱{{ number_format($counts['student'] * ($fares['student'] ?? 80), 2) }}</span>
                        </div>
                        @endif
                    </div>

                    <!-- Total -->
                    <div class="border-t border-gray-200 pt-4">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-900">Total Amount</span>
                            <span class="text-2xl font-bold text-blue-600">₱{{ number_format($grandTotal, 2) }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">All fees included</p>
                    </div>

                    <!-- Security Notice -->
                    <div class="mt-6 p-3 bg-green-50 rounded-lg">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-medium text-green-800">Secure Payment</span>
                        </div>
                        <p class="text-xs text-green-700 mt-1">Your payment information is encrypted and secure.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Digital Wallet Confirmation -->
        <div id="confirmation" class="hidden mt-8 bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Payment Confirmation</h2>
            <div class="text-center">
                <img id="qr-image" src="" alt="QR Code" class="w-48 h-48 mx-auto mb-4 border rounded">
                <p id="account-name" class="text-lg font-semibold mb-2"></p>
                <p id="account-number" class="text-gray-600 mb-4"></p>
                <p class="text-sm text-gray-500">Please scan the QR code using your wallet app and complete the payment. Wait for staff confirmation.</p>
            </div>
            <div id="status-message" class="mt-6 text-center">
                <p>Waiting for payment confirmation...</p>
            </div>
        </div>
    </div>
</div>

<script>
const wallets = @json($wallets ?? []);
const digitalWalletTypes = wallets.map(w => w.type);

document.addEventListener('DOMContentLoaded', function() {
    const paymentOptions = document.querySelectorAll('.payment-option');
    const cardDetails = document.getElementById('cardDetails');
    const paymentForm = document.getElementById('paymentForm');
    
    paymentOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Remove selected state from all options
            paymentOptions.forEach(opt => {
                opt.classList.remove('border-blue-300', 'bg-blue-50');
                opt.classList.add('border-gray-200');
                opt.querySelector('.payment-check').classList.add('hidden');
            });
            
            // Add selected state to clicked option
            this.classList.remove('border-gray-200');
            this.classList.add('border-blue-300', 'bg-blue-50');
            this.querySelector('.payment-check').classList.remove('hidden');
            
            // Check the radio button
            this.querySelector('input[type="radio"]').checked = true;
            
            // Show/hide card details and update form action
            const paymentMethod = this.querySelector('input[type="radio"]').value;
            
            // Update form action based on payment method
            if (digitalWalletTypes.includes(paymentMethod)) {
                // Digital wallet - use separate route
                paymentForm.action = '{{ route("bookings.process.digital-wallet") }}';
            } else {
                // PayMongo/Card/COD - use regular route
                paymentForm.action = '{{ route("bookings.process") }}';
            }
            
            // Toggle extra fields for wallets
            document.querySelectorAll('.paymaya-extra').forEach(el => el.classList.add('hidden'));

            if (paymentMethod === 'card') {
                cardDetails.classList.remove('hidden');
            } else {
                cardDetails.classList.add('hidden');
            }

            if (paymentMethod === 'paymaya') {
                document.querySelectorAll('.paymaya-extra').forEach(el => el.classList.remove('hidden'));
            }
        });
    });
    
    // Format card number input
    const cardNumberInput = document.querySelector('input[name="card_number"]');
    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formattedValue;
        });
    }
    
    // Format expiry date input
    const expiryInput = document.querySelector('input[name="card_expiry"]');
    if (expiryInput) {
        expiryInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value;
        });
    }
    
    // Set initial form action based on default selected payment method
    const defaultSelected = document.querySelector('input[name="payment_method"]:checked');
    if (defaultSelected) {
        const defaultMethod = defaultSelected.value;
        if (digitalWalletTypes.includes(defaultMethod)) {
            paymentForm.action = '{{ route("bookings.process.digital-wallet") }}';
        } else {
            paymentForm.action = '{{ route("bookings.process") }}';
        }
    }
});
</script>
</body>
</html>