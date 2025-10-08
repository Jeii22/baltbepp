@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-4 md:p-6">
    <h1 class="text-2xl font-bold">Available Trips</h1>
    <p class="text-gray-600 mt-1">{{ $criteria['origin'] }} → {{ $criteria['destination'] }} on {{ \Carbon\Carbon::parse($criteria['departure_date'])->format('M d, Y') }}</p>
    @if(($criteria['tripType'] ?? '') === 'round' && !empty($criteria['return_date']))
        <p class="text-gray-600">Return: {{ $criteria['destination'] }} → {{ $criteria['origin'] }} on {{ \Carbon\Carbon::parse($criteria['return_date'])->format('M d, Y') }}</p>
    @endif

    <div class="grid md:grid-cols-2 gap-6 mt-6">
        <div>
            <h2 class="text-xl font-semibold mb-2">Outbound</h2>
            @forelse($outbound as $trip)
                <div class="border rounded-lg p-4 mb-3 flex items-center justify-between">
                    <div>
                        <p class="font-medium">{{ $trip->origin }} → {{ $trip->destination }}</p>
                        <p class="text-sm text-gray-600">Depart: {{ \Carbon\Carbon::parse($trip->departure_time)->format('M d, Y h:i A') }}</p>
                        <p class="text-sm text-gray-600">Arrive: {{ \Carbon\Carbon::parse($trip->arrival_time)->format('M d, Y h:i A') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold">₱{{ number_format($trip->price, 2) }}</p>
                        <a href="{{ route('bookings.create', $trip->id) }}" class="inline-block mt-2 bg-blue-600 text-white px-4 py-2 rounded">Select</a>
                    </div>
                </div>
            @empty
                <p class="text-gray-600">No outbound trips found.</p>
            @endforelse
        </div>

        @if(($criteria['tripType'] ?? '') === 'round')
        <div>
            <h2 class="text-xl font-semibold mb-2">Return</h2>
            @forelse($inbound as $trip)
                <div class="border rounded-lg p-4 mb-3 flex items-center justify-between">
                    <div>
                        <p class="font-medium">{{ $trip->origin }} → {{ $trip->destination }}</p>
                        <p class="text-sm text-gray-600">Depart: {{ \Carbon\Carbon::parse($trip->departure_time)->format('M d, Y h:i A') }}</p>
                        <p class="text-sm text-gray-600">Arrive: {{ \Carbon\Carbon::parse($trip->arrival_time)->format('M d, Y h:i A') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold">₱{{ number_format($trip->price, 2) }}</p>
                        <a href="{{ route('bookings.create', $trip->id) }}" class="inline-block mt-2 bg-blue-600 text-white px-4 py-2 rounded">Select</a>
                    </div>
                </div>
            @empty
                <p class="text-gray-600">No return trips found.</p>
            @endforelse
        </div>
        @endif
    </div>
</div>
@endsection