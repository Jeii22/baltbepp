@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-4 md:p-6">
    <h1 class="text-2xl font-bold mb-4">Booking Summary</h1>

    <div class="border rounded-lg p-4 mb-6">
        <p class="font-medium">{{ $trip->origin }} → {{ $trip->destination }}</p>
        <p class="text-sm text-gray-600">Depart: {{ \Carbon\Carbon::parse($trip->departure_time)->format('M d, Y h:i A') }}</p>
        <p class="text-sm text-gray-600">Arrive: {{ \Carbon\Carbon::parse($trip->arrival_time)->format('M d, Y h:i A') }}</p>
    </div>

    <div class="border rounded-lg p-4 mb-6">
        <h2 class="text-lg font-semibold mb-2">Passenger Breakdown</h2>
        <ul class="text-sm text-gray-700 space-y-1">
            <li>Adult × {{ $counts['adult'] }} @ ₱{{ number_format($fares['adult'],2) }}</li>
            <li>Child × {{ $counts['child'] }} @ ₱{{ number_format($fares['child'],2) }}</li>
            <li>Infant × {{ $counts['infant'] }} @ ₱{{ number_format($fares['infant'],2) }}</li>
            <li>PWD/Senior × {{ $counts['pwd'] }} @ ₱{{ number_format($fares['pwd'],2) }}</li>
        </ul>
        <div class="mt-3 text-right text-sm text-gray-600">
            <p>Trip Base Price: ₱{{ number_format($trip->price,2) }} × {{ $counts['adult'] + $counts['child'] + $counts['infant'] + $counts['pwd'] }}</p>
            <p>Passenger Fares Total: ₱{{ number_format(($counts['adult']*$fares['adult']) + ($counts['child']*$fares['child']) + ($counts['infant']*$fares['infant']) + ($counts['pwd']*$fares['pwd']),2) }}</p>
        </div>
        <div class="mt-2 text-right">
            <p class="text-xl font-bold">Subtotal: ₱{{ number_format($subtotal, 2) }}</p>
        </div>
    </div>

    <div class="border rounded-lg p-4 mb-6">
        <h2 class="text-lg font-semibold mb-2">Contact</h2>
        <p class="text-sm text-gray-700"><span class="font-medium">Name:</span> {{ $customer['full_name'] }}</p>
        <p class="text-sm text-gray-700"><span class="font-medium">Email:</span> {{ $customer['email'] }}</p>
        @if(!empty($customer['phone']))
            <p class="text-sm text-gray-700"><span class="font-medium">Phone:</span> {{ $customer['phone'] }}</p>
        @endif
    </div>

    <form action="{{ route('bookings.checkout') }}" method="POST" class="flex justify-end gap-3">
        @csrf
        <a href="{{ url()->previous() }}" class="px-4 py-2 bg-gray-200 rounded">Back</a>
        <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded">Proceed to Payment</button>
    </form>
</div>
@endsection