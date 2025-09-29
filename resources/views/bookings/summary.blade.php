<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Booking Summary - Balt-Bep</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-white text-gray-800">

    <!-- Navbar (same as welcome.blade) -->
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

    <!-- Hero Section -->
    <div class="relative bg-cover bg-center h-[45vh] md:h-[55vh]" style="background-image: url('/images/barko.png');">
        <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
            <div class="text-center text-white px-6">
                <h1 class="text-3xl md:text-5xl font-bold">Booking Summary</h1>
                <p class="mt-2 text-lg md:text-2xl italic">Review your booking details</p>
            </div>
        </div>
    </div>

    <!-- Content Card -->
    <div class="relative -mt-16 max-w-6xl mx-auto bg-white/90 backdrop-blur-md rounded-2xl shadow-2xl ring-1 ring-black/5 p-6 md:p-8">
        @include('bookings.partials.progress', ['current' => 'review'])

        <div class="grid lg:grid-cols-3 gap-8 mt-6">
            <!-- Main Summary Content -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Trip Details -->
                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center mr-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900">Trip Details</h3>
                            <p class="text-sm text-gray-600">Your selected journey</p>
                        </div>
                    </div>
                    
                    <!-- Outbound Trip -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-semibold text-blue-900">Outbound Journey</h4>
                            <span class="text-sm bg-blue-600 text-white px-2 py-1 rounded">{{ $outboundTrip->vessel_name ?? 'MV Balt-Bep' }}</span>
                        </div>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-lg font-semibold text-gray-900">{{ $outboundTrip->origin }} → {{ $outboundTrip->destination }}</p>
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Departure:</span> 
                                    {{ \Carbon\Carbon::parse($outboundTrip->departure_time)->format('M d, Y h:i A') }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Arrival:</span> 
                                    {{ \Carbon\Carbon::parse($outboundTrip->arrival_time)->format('M d, Y h:i A') }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600">Base Fare</p>
                                <p class="text-2xl font-bold text-blue-600">₱{{ number_format($outboundTrip->price ?? 0, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Return Trip (if applicable) -->
                    @if(isset($inboundTrip) && $inboundTrip)
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-semibold text-green-900">Return Journey</h4>
                            <span class="text-sm bg-green-600 text-white px-2 py-1 rounded">{{ $inboundTrip->vessel_name ?? 'MV Balt-Bep' }}</span>
                        </div>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-lg font-semibold text-gray-900">{{ $inboundTrip->origin }} → {{ $inboundTrip->destination }}</p>
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Departure:</span> 
                                    {{ \Carbon\Carbon::parse($inboundTrip->departure_time)->format('M d, Y h:i A') }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    <span class="font-medium">Arrival:</span> 
                                    {{ \Carbon\Carbon::parse($inboundTrip->arrival_time)->format('M d, Y h:i A') }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600">Base Fare</p>
                                <p class="text-2xl font-bold text-green-600">₱{{ number_format($inboundTrip->price ?? 0, 2) }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Passenger Details -->
                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-green-600 text-white rounded-full flex items-center justify-center mr-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900">Passenger Information</h3>
                            <p class="text-sm text-gray-600">{{ count($passengers ?? []) }} passenger{{ count($passengers ?? []) > 1 ? 's' : '' }} traveling</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        @if(isset($passengers) && is_array($passengers))
                            @foreach($passengers as $index => $passenger)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center mb-3">
                                    @php
                                        $colors = [
                                            'adult' => 'bg-blue-600',
                                            'child' => 'bg-green-600', 
                                            'infant' => 'bg-yellow-600',
                                            'pwd' => 'bg-purple-600',
                                            'student' => 'bg-indigo-600'
                                        ];
                                        $typeLabels = [
                                            'adult' => 'Adult',
                                            'child' => 'Child',
                                            'infant' => 'Infant', 
                                            'pwd' => 'PWD/Senior',
                                            'student' => 'Student'
                                        ];
                                    @endphp
                                    <div class="w-8 h-8 {{ $colors[$passenger['type']] ?? 'bg-gray-600' }} text-white rounded-full flex items-center justify-center font-semibold text-sm mr-3">
                                        {{ $index }}
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900">
                                            {{ $passenger['first_name'] ?? '' }} {{ $passenger['last_name'] ?? '' }}
                                        </h4>
                                        <p class="text-sm text-gray-600">{{ $typeLabels[$passenger['type']] ?? 'Passenger' }} • ₱{{ number_format($passenger['fare'] ?? 0, 2) }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-sm text-gray-500">{{ ucfirst($passenger['gender'] ?? '') }}</span>
                                        @if(isset($passenger['birth_date']))
                                        <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($passenger['birth_date'])->format('M d, Y') }}</p>
                                        @endif
                                    </div>
                                </div>
                                
                                @if(isset($passenger['student_id']) || isset($passenger['id_number']) || isset($passenger['school']))
                                <div class="text-xs text-gray-500 mt-2 pt-2 border-t border-gray-100">
                                    @if(isset($passenger['student_id']))
                                        <p><span class="font-medium">Student ID:</span> {{ $passenger['student_id'] }}</p>
                                    @endif
                                    @if(isset($passenger['school']))
                                        <p><span class="font-medium">School:</span> {{ $passenger['school'] }}</p>
                                    @endif
                                    @if(isset($passenger['id_number']))
                                        <p><span class="font-medium">PWD/Senior ID:</span> {{ $passenger['id_number'] }}</p>
                                    @endif
                                </div>
                                @endif
                            </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-cyan-600 text-white rounded-full flex items-center justify-center mr-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900">Contact Information</h3>
                            <p class="text-sm text-gray-600">Primary contact for this booking</p>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Contact Person</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $contactInfo['contact_name'] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Email Address</p>
                            <p class="text-lg text-gray-900">{{ $contactInfo['contact_email'] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Primary Phone</p>
                            <p class="text-lg text-gray-900">{{ $contactInfo['contact_phone'] ?? 'N/A' }}</p>
                        </div>
                        @if(!empty($contactInfo['contact_phone_alt']))
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Alternative Phone</p>
                            <p class="text-lg text-gray-900">{{ $contactInfo['contact_phone_alt'] }}</p>
                        </div>
                        @endif
                    </div>

                    @if(!empty($contactInfo['contact_address']))
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <p class="text-sm text-gray-600 font-medium">Address</p>
                        <p class="text-gray-900">{{ $contactInfo['contact_address'] }}</p>
                    </div>
                    @endif

                    @if(!empty($contactInfo['special_requests']))
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <p class="text-sm text-gray-600 font-medium">Special Requests</p>
                        <p class="text-gray-900">{{ $contactInfo['special_requests'] }}</p>
                    </div>
                    @endif

                    @if(!empty($contactInfo['contact_preferences']))
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <p class="text-sm text-gray-600 font-medium">Communication Preferences</p>
                        <div class="flex flex-wrap gap-2 mt-1">
                            @foreach($contactInfo['contact_preferences'] as $pref)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-cyan-100 text-cyan-800">
                                {{ ucfirst($pref) }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Pricing Summary Sidebar -->
            <div class="lg:col-span-1">
                <div class="sticky top-4 space-y-4">
                    <!-- Fare Breakdown -->
                    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Fare Breakdown</h4>
                        
                        <div class="space-y-3">
                            @php
                                $totalFare = 0;
                                $passengerCounts = [
                                    'adult' => 0, 'child' => 0, 'infant' => 0, 'pwd' => 0, 'student' => 0
                                ];
                                $passengerFares = [
                                    'adult' => 0, 'child' => 0, 'infant' => 0, 'pwd' => 0, 'student' => 0
                                ];
                                
                                if(isset($passengers) && is_array($passengers)) {
                                    foreach($passengers as $passenger) {
                                        $type = $passenger['type'] ?? 'adult';
                                        $fare = floatval($passenger['fare'] ?? 0);
                                        $passengerCounts[$type]++;
                                        $passengerFares[$type] = $fare;
                                        $totalFare += $fare;
                                    }
                                }
                            @endphp
                            
                            @foreach(['adult', 'child', 'infant', 'pwd', 'student'] as $type)
                                @if($passengerCounts[$type] > 0)
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-700">
                                        {{ ucfirst($type === 'pwd' ? 'PWD/Senior' : $type) }} × {{ $passengerCounts[$type] }}
                                    </span>
                                    <span class="font-medium">₱{{ number_format($passengerFares[$type] * $passengerCounts[$type], 2) }}</span>
                                </div>
                                @endif
                            @endforeach
                        </div>
                        
                        <div class="border-t border-gray-200 mt-4 pt-4">
                            <div class="flex justify-between items-center">
                                <span class="font-semibold">Subtotal per trip:</span>
                                <span class="font-semibold">₱{{ number_format($totalFare, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Total Cost -->
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-6 shadow-sm">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Total Cost</h4>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">Outbound Trip:</span>
                                <span class="font-medium">₱{{ number_format($totalFare, 2) }}</span>
                            </div>
                            
                            @if(isset($inboundTrip) && $inboundTrip)
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">Return Trip:</span>
                                <span class="font-medium">₱{{ number_format($totalFare, 2) }}</span>
                            </div>
                            @endif
                        </div>
                        
                        <div class="border-t border-green-200 mt-4 pt-4">
                            <div class="flex justify-between items-center">
                                <span class="text-xl font-bold text-gray-900">Grand Total:</span>
                                <span class="text-2xl font-bold text-green-600">
                                    ₱{{ number_format($totalFare * (isset($inboundTrip) && $inboundTrip ? 2 : 1), 2) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Important Notes -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                        <h5 class="font-semibold text-yellow-800 mb-2">Important Notes</h5>
                        <ul class="text-xs text-yellow-700 space-y-1">
                            <li>• Please arrive at the terminal 30 minutes before departure</li>
                            <li>• Bring valid ID for all passengers</li>
                            <li>• PWD/Senior passengers must present valid ID for discounts</li>
                            <li>• Students must present valid school ID</li>
                            <li>• Booking confirmation will be sent to your email</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between pt-8 border-t border-gray-200 mt-8">
            <a href="{{ url()->previous() }}" class="inline-flex items-center px-6 py-3 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Passenger Details
            </a>
            
            <form action="{{ route('bookings.checkout') }}" method="POST" class="inline">
                @csrf
                <!-- Pass all booking data as hidden inputs -->
                @if(isset($passengers) && is_array($passengers))
                    @foreach($passengers as $index => $passenger)
                        @foreach($passenger as $key => $value)
                            <input type="hidden" name="passengers[{{ $index }}][{{ $key }}]" value="{{ $value }}">
                        @endforeach
                    @endforeach
                @endif
                
                @if(isset($contactInfo) && is_array($contactInfo))
                    @foreach($contactInfo as $key => $value)
                        @if(is_array($value))
                            @foreach($value as $subValue)
                                <input type="hidden" name="{{ $key }}[]" value="{{ $subValue }}">
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                @endif
                
                <input type="hidden" name="trip_id" value="{{ $outboundTrip->id ?? '' }}">
                @if(isset($inboundTrip) && $inboundTrip)
                    <input type="hidden" name="return_trip_id" value="{{ $inboundTrip->id }}">
                @endif
                
                <button type="submit" class="inline-flex items-center px-8 py-3 rounded-lg bg-green-600 text-white hover:bg-green-700 shadow-lg transition-colors">
                    Proceed to Payment
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>

</body>
</html>