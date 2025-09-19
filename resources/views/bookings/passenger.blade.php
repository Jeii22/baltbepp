<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Passenger Details - Balt-Bep</title>
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
                <a href="#refund" class="hover:text-cyan-200">Refund & Rebooking</a>
                <a href="#info" class="hover:text-cyan-200">Travel Info</a>
                <a href="#updates" class="hover:text-cyan-200">Latest Updates</a>
                <a href="#contact" class="hover:text-cyan-200">Contact Us</a>
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
                    {{ $criteria['origin'] ?? 'Unknown' }} → {{ $criteria['destination'] ?? 'Unknown' }}
                    @php
                        try {
                            if (!empty($criteria['departure_date']) && $criteria['departure_date'] !== 'undefined') {
                                echo ' on ' . \Carbon\Carbon::parse($criteria['departure_date'])->format('M d, Y');
                            }
                        } catch (Exception $e) {
                            // Ignore date parsing errors
                        }
                    @endphp
                </p>
                @php
                    try {
                        if (($criteria['tripType'] ?? '') === 'round' && !empty($criteria['return_date']) && $criteria['return_date'] !== 'undefined') {
                            echo '<p class="text-white/90 mt-1">';
                            echo 'Return: ' . ($criteria['destination'] ?? 'Unknown') . ' → ' . ($criteria['origin'] ?? 'Unknown') . ' on ';
                            echo \Carbon\Carbon::parse($criteria['return_date'])->format('M d, Y');
                            echo '</p>';
                        }
                    } catch (Exception $e) {
                        // Ignore date parsing errors
                    }
                @endphp
            </div>
        </div>
    </div>

    <!-- Content Card -->
    <div class="relative -mt-16 max-w-6xl mx-auto bg-white/90 backdrop-blur-md rounded-2xl shadow-2xl ring-1 ring-black/5 p-6 md:p-8">
        @include('bookings.partials.progress', ['current' => 'passenger'])

        <div class="grid md:grid-cols-3 gap-6 mt-6">
            <!-- Passenger Forms -->
            <div class="md:col-span-2">
                <form id="passengerForm" action="{{ route('bookings.summary') }}" method="POST" class="space-y-8">
                    @csrf
                    <input type="hidden" name="trip_id" value="{{ $outboundTrip->id }}">
                    @if(isset($inboundTrip) && $inboundTrip)
                        <input type="hidden" name="return_trip_id" value="{{ $inboundTrip->id }}">
                    @endif
                    
                    <!-- Passenger Count Summary -->
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <h3 class="text-lg font-semibold text-blue-900 mb-3">Passenger Summary</h3>
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 text-sm">
                            @php
                                $adultCount = $criteria['adult'] ?? 1;
                                $childCount = $criteria['child'] ?? 0;
                                $infantCount = $criteria['infant'] ?? 0;
                                $pwdCount = $criteria['pwd'] ?? 0;
                                $studentCount = $criteria['student'] ?? 0;
                                $totalPassengers = $adultCount + $childCount + $infantCount + $pwdCount + $studentCount;
                            @endphp
                            
                            @if($adultCount > 0)
                            <div class="text-center">
                                <div class="font-semibold text-blue-700">{{ $adultCount }}</div>
                                <div class="text-blue-600">Adult{{ $adultCount > 1 ? 's' : '' }}</div>
                            </div>
                            @endif
                            
                            @if($childCount > 0)
                            <div class="text-center">
                                <div class="font-semibold text-blue-700">{{ $childCount }}</div>
                                <div class="text-blue-600">Child{{ $childCount > 1 ? 'ren' : '' }}</div>
                            </div>
                            @endif
                            
                            @if($infantCount > 0)
                            <div class="text-center">
                                <div class="font-semibold text-blue-700">{{ $infantCount }}</div>
                                <div class="text-blue-600">Infant{{ $infantCount > 1 ? 's' : '' }}</div>
                            </div>
                            @endif
                            
                            @if($pwdCount > 0)
                            <div class="text-center">
                                <div class="font-semibold text-blue-700">{{ $pwdCount }}</div>
                                <div class="text-blue-600">PWD/Senior{{ $pwdCount > 1 ? 's' : '' }}</div>
                            </div>
                            @endif
                            
                            @if($studentCount > 0)
                            <div class="text-center">
                                <div class="font-semibold text-blue-700">{{ $studentCount }}</div>
                                <div class="text-blue-600">Student{{ $studentCount > 1 ? 's' : '' }}</div>
                            </div>
                            @endif
                        </div>
                        <div class="mt-3 text-center">
                            <span class="text-sm text-blue-700">Total: <strong>{{ $totalPassengers }} passenger{{ $totalPassengers > 1 ? 's' : '' }}</strong></span>
                        </div>
                    </div>

                    <!-- Individual Passenger Details -->
                    <div class="space-y-6">
                        @php $passengerIndex = 1; @endphp
                        
                        <!-- Adult Passengers -->
                        @for($i = 1; $i <= $adultCount; $i++)
                        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                            <div class="flex items-center mb-4">
                                <div class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-semibold mr-3">
                                    {{ $passengerIndex }}
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900">Passenger {{ $passengerIndex }} - Adult</h4>
                                    <p class="text-sm text-gray-600">Regular fare: ₱{{ number_format($fares['Regular'] ?? 900, 2) }}</p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-semibold text-gray-700">First Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="passengers[{{ $passengerIndex }}][first_name]" class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Juan" required>
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-gray-700">Last Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="passengers[{{ $passengerIndex }}][last_name]" class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Dela Cruz" required>
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-gray-700">Gender <span class="text-red-500">*</span></label>
                                    <select name="passengers[{{ $passengerIndex }}][gender]" class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                        <option value="">Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-gray-700">Date of Birth <span class="text-red-500">*</span></label>
                                    <input type="date" name="passengers[{{ $passengerIndex }}][birth_date]" class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                </div>
                            </div>
                            
                            <input type="hidden" name="passengers[{{ $passengerIndex }}][type]" value="adult">
                            <input type="hidden" name="passengers[{{ $passengerIndex }}][fare]" value="{{ $fares['Regular'] ?? 900 }}">
                        </div>
                        @php $passengerIndex++; @endphp
                        @endfor

                        <!-- Child Passengers -->
                        @for($i = 1; $i <= $childCount; $i++)
                        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                            <div class="flex items-center mb-4">
                                <div class="w-10 h-10 bg-green-600 text-white rounded-full flex items-center justify-center font-semibold mr-3">
                                    {{ $passengerIndex }}
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900">Passenger {{ $passengerIndex }} - Child</h4>
                                    <p class="text-sm text-gray-600">Child fare (2-11 years): ₱{{ number_format($fares['Child (2-11)'] ?? 450, 2) }}</p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-semibold text-gray-700">First Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="passengers[{{ $passengerIndex }}][first_name]" class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500" placeholder="Maria" required>
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-gray-700">Last Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="passengers[{{ $passengerIndex }}][last_name]" class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500" placeholder="Dela Cruz" required>
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-gray-700">Gender <span class="text-red-500">*</span></label>
                                    <select name="passengers[{{ $passengerIndex }}][gender]" class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
                                        <option value="">Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-gray-700">Date of Birth <span class="text-red-500">*</span></label>
                                    <input type="date" name="passengers[{{ $passengerIndex }}][birth_date]" class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
                                    <p class="text-xs text-gray-500 mt-1">Must be between 2-11 years old</p>
                                </div>
                            </div>
                            
                            <input type="hidden" name="passengers[{{ $passengerIndex }}][type]" value="child">
                            <input type="hidden" name="passengers[{{ $passengerIndex }}][fare]" value="{{ $fares['Child (2-11)'] ?? 450 }}">
                        </div>
                        @php $passengerIndex++; @endphp
                        @endfor

                        <!-- Infant Passengers -->
                        @for($i = 1; $i <= $infantCount; $i++)
                        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                            <div class="flex items-center mb-4">
                                <div class="w-10 h-10 bg-yellow-600 text-white rounded-full flex items-center justify-center font-semibold mr-3">
                                    {{ $passengerIndex }}
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900">Passenger {{ $passengerIndex }} - Infant</h4>
                                    <p class="text-sm text-gray-600">Infant fare (under 2 years): ₱{{ number_format($fares['Infant'] ?? 0, 2) }}</p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-semibold text-gray-700">First Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="passengers[{{ $passengerIndex }}][first_name]" class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500" placeholder="Baby" required>
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-gray-700">Last Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="passengers[{{ $passengerIndex }}][last_name]" class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500" placeholder="Dela Cruz" required>
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-gray-700">Gender <span class="text-red-500">*</span></label>
                                    <select name="passengers[{{ $passengerIndex }}][gender]" class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500" required>
                                        <option value="">Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-gray-700">Date of Birth <span class="text-red-500">*</span></label>
                                    <input type="date" name="passengers[{{ $passengerIndex }}][birth_date]" class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500" required>
                                    <p class="text-xs text-gray-500 mt-1">Must be under 2 years old</p>
                                </div>
                            </div>
                            
                            <input type="hidden" name="passengers[{{ $passengerIndex }}][type]" value="infant">
                            <input type="hidden" name="passengers[{{ $passengerIndex }}][fare]" value="{{ $fares['Infant'] ?? 0 }}">
                        </div>
                        @php $passengerIndex++; @endphp
                        @endfor

                        <!-- PWD/Senior Passengers -->
                        @for($i = 1; $i <= $pwdCount; $i++)
                        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                            <div class="flex items-center mb-4">
                                <div class="w-10 h-10 bg-purple-600 text-white rounded-full flex items-center justify-center font-semibold mr-3">
                                    {{ $passengerIndex }}
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900">Passenger {{ $passengerIndex }} - PWD/Senior</h4>
                                    <p class="text-sm text-gray-600">PWD/Senior Citizen fare: ₱{{ number_format($fares['Senior Citizen / PWD'] ?? 720, 2) }}</p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-semibold text-gray-700">First Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="passengers[{{ $passengerIndex }}][first_name]" class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-purple-500" placeholder="Jose" required>
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-gray-700">Last Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="passengers[{{ $passengerIndex }}][last_name]" class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-purple-500" placeholder="Dela Cruz" required>
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-gray-700">Gender <span class="text-red-500">*</span></label>
                                    <select name="passengers[{{ $passengerIndex }}][gender]" class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required>
                                        <option value="">Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-gray-700">Date of Birth <span class="text-red-500">*</span></label>
                                    <input type="date" name="passengers[{{ $passengerIndex }}][birth_date]" class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="text-sm font-semibold text-gray-700">PWD/Senior ID Number <span class="text-red-500">*</span></label>
                                    <input type="text" name="passengers[{{ $passengerIndex }}][id_number]" class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-purple-500" placeholder="PWD/Senior Citizen ID Number" required>
                                    <p class="text-xs text-gray-500 mt-1">Required for discount verification</p>
                                </div>
                            </div>
                            
                            <input type="hidden" name="passengers[{{ $passengerIndex }}][type]" value="pwd">
                            <input type="hidden" name="passengers[{{ $passengerIndex }}][fare]" value="{{ $fares['Senior Citizen / PWD'] ?? 720 }}">
                        </div>
                        @php $passengerIndex++; @endphp
                        @endfor

                        <!-- Student Passengers -->
                        @for($i = 1; $i <= $studentCount; $i++)
                        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                            <div class="flex items-center mb-4">
                                <div class="w-10 h-10 bg-indigo-600 text-white rounded-full flex items-center justify-center font-semibold mr-3">
                                    {{ $passengerIndex }}
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900">Passenger {{ $passengerIndex }} - Student</h4>
                                    <p class="text-sm text-gray-600">Student fare: ₱{{ number_format($fares['Student'] ?? 765, 2) }}</p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-semibold text-gray-700">First Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="passengers[{{ $passengerIndex }}][first_name]" class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Anna" required>
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-gray-700">Last Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="passengers[{{ $passengerIndex }}][last_name]" class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Dela Cruz" required>
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-gray-700">Gender <span class="text-red-500">*</span></label>
                                    <select name="passengers[{{ $passengerIndex }}][gender]" class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                                        <option value="">Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-gray-700">Date of Birth <span class="text-red-500">*</span></label>
                                    <input type="date" name="passengers[{{ $passengerIndex }}][birth_date]" class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-gray-700">Student ID Number <span class="text-red-500">*</span></label>
                                    <input type="text" name="passengers[{{ $passengerIndex }}][student_id]" class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Student ID Number" required>
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-gray-700">School/University <span class="text-red-500">*</span></label>
                                    <input type="text" name="passengers[{{ $passengerIndex }}][school]" class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="University of the Philippines" required>
                                </div>
                            </div>
                            
                            <input type="hidden" name="passengers[{{ $passengerIndex }}][type]" value="student">
                            <input type="hidden" name="passengers[{{ $passengerIndex }}][fare]" value="{{ $fares['Student'] ?? 765 }}">
                        </div>
                        @php $passengerIndex++; @endphp
                        @endfor
                    </div>

                    <!-- Contact Details Section -->
                    <div class="bg-gradient-to-r from-cyan-50 to-blue-50 border border-cyan-200 rounded-xl p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-cyan-600 text-white rounded-full flex items-center justify-center mr-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900">Contact Information</h4>
                                <p class="text-sm text-gray-600">Primary contact details for booking updates and communications</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="text-sm font-semibold text-gray-700">Primary Contact Name <span class="text-red-500">*</span></label>
                                <input type="text" name="contact_name" class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500" placeholder="Juan Dela Cruz" required>
                                <p class="text-xs text-gray-500 mt-1">Person responsible for this booking</p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-semibold text-gray-700">Contact Email <span class="text-red-500">*</span></label>
                                <input type="email" name="contact_email" class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500" placeholder="juan@example.com" required>
                                <p class="text-xs text-gray-500 mt-1">Booking confirmation and updates will be sent here</p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-semibold text-gray-700">Primary Phone Number <span class="text-red-500">*</span></label>
                                <input type="tel" name="contact_phone" class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500" placeholder="0917 123 4567" required>
                                <p class="text-xs text-gray-500 mt-1">For urgent booking notifications and changes</p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-semibold text-gray-700">Alternative Phone Number</label>
                                <input type="tel" name="contact_phone_alt" class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500" placeholder="0918 987 6543">
                                <p class="text-xs text-gray-500 mt-1">Backup contact number (optional)</p>
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="text-sm font-semibold text-gray-700">Complete Address</label>
                                <textarea name="contact_address" rows="3" class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 resize-none" placeholder="123 Main Street, Barangay Sample, City, Province, 1234">{{ old('contact_address') }}</textarea>
                                <p class="text-xs text-gray-500 mt-1">Complete address for emergency contact purposes (optional)</p>
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="text-sm font-semibold text-gray-700">Special Requests or Notes</label>
                                <textarea name="special_requests" rows="3" class="mt-1 block w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 resize-none" placeholder="Any special assistance needed, dietary requirements, accessibility needs, etc.">{{ old('special_requests') }}</textarea>
                                <p class="text-xs text-gray-500 mt-1">Let us know if you need any special assistance or have specific requirements (optional)</p>
                            </div>
                        </div>
                        
                        <!-- Contact Preferences -->
                        <div class="mt-6 pt-4 border-t border-cyan-200">
                            <h5 class="text-sm font-semibold text-gray-700 mb-3">Communication Preferences</h5>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="flex items-center">
                                    <input type="checkbox" name="contact_preferences[]" value="email" id="pref_email" class="h-4 w-4 text-cyan-600 focus:ring-cyan-500 border-gray-300 rounded" checked>
                                    <label for="pref_email" class="ml-2 text-sm text-gray-700">Email notifications</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" name="contact_preferences[]" value="sms" id="pref_sms" class="h-4 w-4 text-cyan-600 focus:ring-cyan-500 border-gray-300 rounded" checked>
                                    <label for="pref_sms" class="ml-2 text-sm text-gray-700">SMS updates</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" name="contact_preferences[]" value="call" id="pref_call" class="h-4 w-4 text-cyan-600 focus:ring-cyan-500 border-gray-300 rounded">
                                    <label for="pref_call" class="ml-2 text-sm text-gray-700">Phone calls (urgent only)</label>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Select how you'd like to receive booking updates and notifications</p>
                        </div>
                    </div>

                    <!-- Hidden inputs for passenger counts -->
                    <input type="hidden" name="adult" value="{{ $adultCount }}">
                    <input type="hidden" name="child" value="{{ $childCount }}">
                    <input type="hidden" name="infant" value="{{ $infantCount }}">
                    <input type="hidden" name="pwd" value="{{ $pwdCount }}">
                    <input type="hidden" name="student" value="{{ $studentCount }}">

                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <a href="{{ route('booking.schedule', request()->only(['origin', 'destination', 'departure_date', 'return_date', 'tripType', 'adult', 'child', 'infant', 'pwd', 'student'])) }}" class="inline-flex items-center px-6 py-3 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Back to Schedule
                        </a>
                        <button type="submit" class="inline-flex items-center px-8 py-3 rounded-lg bg-green-600 text-white hover:bg-green-700 shadow-lg transition-colors">
                            Proceed to Summary
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Sidebar summary -->
            <aside>
                <div class="sticky top-4 space-y-3">
                    <div class="border rounded-xl bg-white p-4 shadow">
                        <h4 class="font-semibold mb-2">Trip Summary</h4>
                        <div class="text-sm text-gray-700 space-y-2">
                            <div>
                                <p class="font-medium">{{ $outboundTrip->origin }} → {{ $outboundTrip->destination }}</p>
                                @php
                                    try {
                                        if ($outboundTrip->departure_time && $outboundTrip->departure_time !== 'undefined') {
                                            echo '<p class="text-xs text-gray-600">Depart: ' . \Carbon\Carbon::parse($outboundTrip->departure_time)->format('M d, Y h:i A') . '</p>';
                                        }
                                    } catch (Exception $e) {
                                        // Ignore date parsing errors
                                    }
                                    
                                    try {
                                        if ($outboundTrip->arrival_time && $outboundTrip->arrival_time !== 'undefined') {
                                            echo '<p class="text-xs text-gray-600">Arrive: ' . \Carbon\Carbon::parse($outboundTrip->arrival_time)->format('M d, Y h:i A') . '</p>';
                                        }
                                    } catch (Exception $e) {
                                        // Ignore date parsing errors
                                    }
                                @endphp
                                <p class="text-lg font-bold mt-1">₱{{ number_format($outboundTrip->price ?? 0, 2) }}</p>
                            </div>
                            
                            @if($inboundTrip)
                            <div class="border-t pt-2">
                                <p class="font-medium">{{ $inboundTrip->origin }} → {{ $inboundTrip->destination }}</p>
                                @php
                                    try {
                                        if ($inboundTrip->departure_time && $inboundTrip->departure_time !== 'undefined') {
                                            echo '<p class="text-xs text-gray-600">Depart: ' . \Carbon\Carbon::parse($inboundTrip->departure_time)->format('M d, Y h:i A') . '</p>';
                                        }
                                    } catch (Exception $e) {
                                        // Ignore date parsing errors
                                    }
                                    
                                    try {
                                        if ($inboundTrip->arrival_time && $inboundTrip->arrival_time !== 'undefined') {
                                            echo '<p class="text-xs text-gray-600">Arrive: ' . \Carbon\Carbon::parse($inboundTrip->arrival_time)->format('M d, Y h:i A') . '</p>';
                                        }
                                    } catch (Exception $e) {
                                        // Ignore date parsing errors
                                    }
                                @endphp
                                <p class="text-lg font-bold mt-1">₱{{ number_format($inboundTrip->price ?? 0, 2) }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="border rounded-xl bg-white p-4 shadow">
                        <h4 class="font-semibold mb-2">Passengers & Fares</h4>
                        <div class="fare-breakdown text-sm text-gray-700 space-y-2">
                            @php
                                $totalFare = 0;
                                $adultCount = $criteria['adult'] ?? 1;
                                $childCount = $criteria['child'] ?? 0;
                                $infantCount = $criteria['infant'] ?? 0;
                                $pwdCount = $criteria['pwd'] ?? 0;
                                $studentCount = $criteria['student'] ?? 0;
                                
                                // Get fare prices (with fallbacks)
                                $adultFare = $fares['Regular'] ?? 900;
                                $childFare = $fares['Child (2-11)'] ?? 450;
                                $infantFare = $fares['Infant'] ?? 0;
                                $pwdFare = $fares['Senior Citizen / PWD'] ?? 720;
                                $studentFare = $fares['Student'] ?? 765;
                            @endphp
                            
                            @if($adultCount > 0)
                            <div class="flex justify-between">
                                <span>Adult × {{ $adultCount }}</span>
                                <span class="font-medium">₱{{ number_format($adultFare * $adultCount, 2) }}</span>
                                @php $totalFare += $adultFare * $adultCount; @endphp
                            </div>
                            @endif
                            
                            @if($childCount > 0)
                            <div class="flex justify-between">
                                <span>Child × {{ $childCount }}</span>
                                <span class="font-medium">₱{{ number_format($childFare * $childCount, 2) }}</span>
                                @php $totalFare += $childFare * $childCount; @endphp
                            </div>
                            @endif
                            
                            @if($infantCount > 0)
                            <div class="flex justify-between">
                                <span>Infant × {{ $infantCount }}</span>
                                <span class="font-medium">₱{{ number_format($infantFare * $infantCount, 2) }}</span>
                                @php $totalFare += $infantFare * $infantCount; @endphp
                            </div>
                            @endif
                            
                            @if($pwdCount > 0)
                            <div class="flex justify-between">
                                <span>PWD/Senior × {{ $pwdCount }}</span>
                                <span class="font-medium">₱{{ number_format($pwdFare * $pwdCount, 2) }}</span>
                                @php $totalFare += $pwdFare * $pwdCount; @endphp
                            </div>
                            @endif
                            
                            @if($studentCount > 0)
                            <div class="flex justify-between">
                                <span>Student × {{ $studentCount }}</span>
                                <span class="font-medium">₱{{ number_format($studentFare * $studentCount, 2) }}</span>
                                @php $totalFare += $studentFare * $studentCount; @endphp
                            </div>
                            @endif
                            
                            <div class="border-t pt-2 mt-2">
                                <div class="flex justify-between font-semibold">
                                    <span>Subtotal per trip:</span>
                                    <span>₱{{ number_format($totalFare, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="border rounded-xl bg-white p-4 shadow">
                        <h4 class="font-semibold mb-2">Total Cost</h4>
                        <div class="total-cost text-sm text-gray-700 space-y-2">
                            <div class="flex justify-between">
                                <span>Outbound Trip:</span>
                                <span class="font-medium">₱{{ number_format($totalFare, 2) }}</span>
                            </div>
                            @if($inboundTrip)
                            <div class="flex justify-between">
                                <span>Return Trip:</span>
                                <span class="font-medium">₱{{ number_format($totalFare, 2) }}</span>
                            </div>
                            @endif
                            <div class="border-t pt-2 mt-2">
                                <div class="flex justify-between font-bold text-lg">
                                    <span>Grand Total:</span>
                                    <span class="text-green-600">₱{{ number_format($totalFare * ($inboundTrip ? 2 : 1), 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <script>
        // Dynamic fare calculation
        function updateFareCalculation() {
            const inputs = document.querySelectorAll('.passenger-input');
            let totalFare = 0;
            let fareBreakdown = '';
            
            inputs.forEach(input => {
                const count = parseInt(input.value) || 0;
                const fare = parseFloat(input.dataset.fare) || 0;
                const type = input.dataset.type;
                const subtotal = count * fare;
                
                if (count > 0) {
                    totalFare += subtotal;
                    const typeName = type.charAt(0).toUpperCase() + type.slice(1);
                    fareBreakdown += `
                        <div class="flex justify-between">
                            <span>${typeName} × ${count}</span>
                            <span class="font-medium">₱${subtotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                        </div>
                    `;
                }
            });
            
            // Update the fare breakdown
            const fareContainer = document.querySelector('.fare-breakdown');
            if (fareContainer) {
                fareContainer.innerHTML = fareBreakdown + `
                    <div class="border-t pt-2 mt-2">
                        <div class="flex justify-between font-semibold">
                            <span>Subtotal per trip:</span>
                            <span>₱${totalFare.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                        </div>
                    </div>
                `;
            }
            
            // Update total cost
            const hasReturn = {{ $inboundTrip ? 'true' : 'false' }};
            const grandTotal = totalFare * (hasReturn ? 2 : 1);
            
            const totalContainer = document.querySelector('.total-cost');
            if (totalContainer) {
                totalContainer.innerHTML = `
                    <div class="flex justify-between">
                        <span>Outbound Trip:</span>
                        <span class="font-medium">₱${totalFare.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                    </div>
                    ${hasReturn ? `
                    <div class="flex justify-between">
                        <span>Return Trip:</span>
                        <span class="font-medium">₱${totalFare.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                    </div>
                    ` : ''}
                    <div class="border-t pt-2 mt-2">
                        <div class="flex justify-between font-bold text-lg">
                            <span>Grand Total:</span>
                            <span class="text-green-600">₱${grandTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                        </div>
                    </div>
                `;
            }
        }
        
        // Add event listeners to passenger inputs
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.passenger-input');
            inputs.forEach(input => {
                input.addEventListener('input', updateFareCalculation);
                input.addEventListener('change', updateFareCalculation);
            });
        });
    </script>

    <!-- Data bridge to avoid Blade vs JS template literal conflicts -->
    <div id="bookingData" data-has-return="{{ $inboundTrip ? 'true' : 'false' }}" class="hidden"></div>

    @verbatim
    <script>
        // Recompute totals safely with verbatim to prevent Blade parsing ${...}
        (function() {
            function updateGrandTotalBlock(totalFare) {
                const bookingDataEl = document.getElementById('bookingData');
                const hasReturn = bookingDataEl && bookingDataEl.dataset.hasReturn === 'true';
                const grandTotal = totalFare * (hasReturn ? 2 : 1);

                const totalContainer = document.querySelector('.total-cost');
                if (totalContainer) {
                    totalContainer.innerHTML = '
                        <div class="flex justify-between">\
                            <span>Outbound Trip:</span>\
                            <span class="font-medium">₱' + totalFare.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</span>\
                        </div>' +
                        (hasReturn ? '
                        <div class="flex justify-between">\
                            <span>Return Trip:</span>\
                            <span class="font-medium">₱' + totalFare.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</span>\
                        </div>' : '') +
                        '
                        <div class="border-t pt-2 mt-2">\
                            <div class="flex justify-between font-bold text-lg">\
                                <span>Grand Total:</span>\
                                <span class="text-green-600">₱' + grandTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</span>\
                            </div>\
                        </div>';
                }
            }

            // If your page already defines updateFareCalculation, hook into it; else expose helper
            window.updateGrandTotalBlock = updateGrandTotalBlock;
        })();
    </script>
    @endverbatim

</body>
</html>