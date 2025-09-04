@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-4 md:p-6">
    <h1 class="text-2xl font-bold mb-4">Passenger Details</h1>

    <div class="border rounded-lg p-4 mb-6">
        <p class="font-medium">{{ $trip->origin }} → {{ $trip->destination }}</p>
        <p class="text-sm text-gray-600">Depart: {{ \Carbon\Carbon::parse($trip->departure_time)->format('M d, Y h:i A') }}</p>
        <p class="text-sm text-gray-600">Arrive: {{ \Carbon\Carbon::parse($trip->arrival_time)->format('M d, Y h:i A') }}</p>
    </div>

    <form action="{{ route('bookings.summary') }}" method="POST" class="space-y-5">
        @csrf
        <input type="hidden" name="trip_id" value="{{ $trip->id }}">

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Adult</label>
                <input type="number" name="adult" value="1" min="1" class="mt-1 block w-full border-gray-300 rounded-md">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Child (2-11)</label>
                <input type="number" name="child" value="0" min="0" class="mt-1 block w-full border-gray-300 rounded-md">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Infant</label>
                <input type="number" name="infant" value="0" min="0" class="mt-1 block w-full border-gray-300 rounded-md">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">PWD / Senior</label>
                <input type="number" name="pwd" value="0" min="0" class="mt-1 block w-full border-gray-300 rounded-md">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Full Name</label>
                <input type="text" name="full_name" required class="mt-1 block w-full border-gray-300 rounded-md" placeholder="Juan Dela Cruz">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" required class="mt-1 block w-full border-gray-300 rounded-md" placeholder="you@example.com">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Phone (optional)</label>
                <input type="text" name="phone" class="mt-1 block w-full border-gray-300 rounded-md" placeholder="0917 123 4567">
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg">Continue</button>
        </div>
    </form>
</div>
@endsection