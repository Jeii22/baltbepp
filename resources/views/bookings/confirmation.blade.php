@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-4 md:p-6">
    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <p class="text-green-800 font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-100 text-green-600 mb-4">
            <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Booking Confirmed!</h1>
        <p class="text-gray-600 text-lg">Thank you for choosing BaltBep Ferry Services</p>
    </div>

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
        <a href="{{ route('welcome') }}" 
           class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium text-center">
            Book Another Trip
        </a>
        <button onclick="window.print()" 
                class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
            Print Confirmation
        </button>
    </div>
</div>
@endsection