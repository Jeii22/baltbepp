@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-4 md:p-6 text-center">
    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 text-green-600 mb-4">✓</div>
    <h1 class="text-2xl font-bold mb-2">Booking Confirmed</h1>
    <p class="text-gray-600 mb-6">Thank you! Your booking has been received.</p>

    <div class="border rounded-lg p-4 text-left inline-block">
        <p class="text-sm text-gray-700"><span class="font-medium">Reference:</span> #{{ $booking->id }}</p>
        <p class="text-sm text-gray-700"><span class="font-medium">Status:</span> {{ ucfirst($booking->status) }}</p>
        <p class="text-sm text-gray-700"><span class="font-medium">Route:</span> {{ $booking->origin }} → {{ $booking->destination }}</p>
        <p class="text-sm text-gray-700"><span class="font-medium">Departure:</span> {{ optional($booking->departure_time)->format('M d, Y h:i A') }}</p>
        <p class="text-sm text-gray-700"><span class="font-medium">Passenger:</span> {{ $booking->full_name }} ({{ $booking->email }})</p>
        <p class="text-sm text-gray-700"><span class="font-medium">Counts:</span> A: {{ $booking->adult }}, C: {{ $booking->child }}, I: {{ $booking->infant }}, PWD: {{ $booking->pwd }}</p>
        <p class="text-sm text-gray-700"><span class="font-medium">Total:</span> ₱{{ number_format($booking->total_amount, 2) }}</p>
    </div>

    <div class="mt-6">
        <a href="{{ route('welcome') }}" class="px-6 py-3 bg-blue-600 text-white rounded">Back to Home</a>
    </div>
</div>
@endsection