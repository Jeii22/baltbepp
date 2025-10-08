@extends('layouts.superadmin')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Trip Management</h1>

    <a href="{{ route('trips.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">+ Add Trip</a>

    <!-- Upcoming Trips -->
    <div class="mt-6">
        <h2 class="text-xl font-semibold mb-2">Upcoming Trips</h2>
        @if($upcomingTrips->isEmpty())
            <p class="text-gray-600">No upcoming trips.</p>
        @else
            <table class="w-full mt-2 border">
                <thead class="bg-blue-100">
                    <tr>
                        <th class="px-4 py-2">Origin</th>
                        <th class="px-4 py-2">Destination</th>
                        <th class="px-4 py-2">Departure</th>
                        <th class="px-4 py-2">Arrival</th>
                        <th class="px-4 py-2">Capacity</th>
                        <th class="px-4 py-2">Price</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($upcomingTrips as $trip)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $trip->origin }}</td>
                        <td class="px-4 py-2">{{ $trip->destination }}</td>
                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($trip->departure_time)->format('M d, Y h:i A') }}</td>
                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($trip->arrival_time)->format('M d, Y h:i A') }}</td>
                        <td class="px-4 py-2">{{ $trip->capacity }}</td>
                        <td class="px-4 py-2">₱{{ number_format($trip->price, 2) }}</td>
                        <td class="px-4 py-2 space-x-2">
                            <a href="{{ route('trips.edit', $trip->id) }}" class="text-blue-600 hover:underline">Edit</a>
                            <form action="{{ route('trips.destroy', $trip->id) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline" onclick="return confirm('Delete this trip?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <!-- Recent Departures / Past Trips -->
    <div class="mt-10">
        <h2 class="text-xl font-semibold mb-2">Recent Departures</h2>
        @if($pastTrips->isEmpty())
            <p class="text-gray-600">No past trips yet.</p>
        @else
            <table class="w-full mt-2 border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2">Origin</th>
                        <th class="px-4 py-2">Destination</th>
                        <th class="px-4 py-2">Departure</th>
                        <th class="px-4 py-2">Arrival</th>
                        <th class="px-4 py-2">Capacity</th>
                        <th class="px-4 py-2">Price</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pastTrips as $trip)
                    <tr class="border-t text-gray-600">
                        <td class="px-4 py-2">{{ $trip->origin }}</td>
                        <td class="px-4 py-2">{{ $trip->destination }}</td>
                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($trip->departure_time)->format('M d, Y h:i A') }}</td>
                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($trip->arrival_time)->format('M d, Y h:i A') }}</td>
                        <td class="px-4 py-2">{{ $trip->capacity }}</td>
                        <td class="px-4 py-2">₱{{ number_format($trip->price, 2) }}</td>
                        <td class="px-4 py-2 space-x-2">
                            <a href="{{ route('trips.edit', $trip->id) }}" class="text-blue-600 hover:underline">Edit</a>
                            <form action="{{ route('trips.destroy', $trip->id) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:underline" onclick="return confirm('Delete this trip?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <p class="text-xs text-gray-500 mt-2">Showing 10 most recent departures.</p>
        @endif
    </div>
</div>
@endsection
