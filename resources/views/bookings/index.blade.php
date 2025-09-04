@extends('layouts.superadmin')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Booking Management</h1>

    @if (session('success'))
        <div class="mb-4 rounded bg-green-100 text-green-800 px-4 py-2">{{ session('success') }}</div>
    @endif

    <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3 mb-4">
        <div>
            <label class="text-xs font-semibold">Origin</label>
            <select name="origin" class="w-full border rounded p-2">
                <option value="">All</option>
                <option value="Bantayan" @selected(request('origin')==='Bantayan')>Bantayan</option>
                <option value="Cadiz" @selected(request('origin')==='Cadiz')>Cadiz</option>
            </select>
        </div>
        <div>
            <label class="text-xs font-semibold">Destination</label>
            <select name="destination" class="w-full border rounded p-2">
                <option value="">All</option>
                <option value="Bantayan" @selected(request('destination')==='Bantayan')>Bantayan</option>
                <option value="Cadiz" @selected(request('destination')==='Cadiz')>Cadiz</option>
            </select>
        </div>
        <div>
            <label class="text-xs font-semibold">Status</label>
            <select name="status" class="w-full border rounded p-2">
                <option value="">All</option>
                <option value="pending" @selected(request('status')==='pending')>Pending</option>
                <option value="confirmed" @selected(request('status')==='confirmed')>Confirmed</option>
                <option value="cancelled" @selected(request('status')==='cancelled')>Cancelled</option>
            </select>
        </div>
        <div class="md:col-span-2 flex items-end gap-2">
            <button class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
            <a href="{{ route('bookings.index') }}" class="px-4 py-2 border rounded">Reset</a>
        </div>
    </form>

    <div class="overflow-x-auto bg-white rounded border">
        <table class="w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2 text-left">ID</th>
                    <th class="px-3 py-2 text-left">Route</th>
                    <th class="px-3 py-2 text-left">Departure</th>
                    <th class="px-3 py-2 text-left">Passenger</th>
                    <th class="px-3 py-2 text-left">Counts</th>
                    <th class="px-3 py-2 text-left">Status</th>
                    <th class="px-3 py-2 text-left">Total</th>
                    <th class="px-3 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bookings as $b)
                    <tr class="border-t">
                        <td class="px-3 py-2 align-top">#{{ $b->id }}</td>
                        <td class="px-3 py-2 align-top">{{ $b->origin }} → {{ $b->destination }}</td>
                        <td class="px-3 py-2 align-top">{{ optional($b->departure_time)->format('M d, Y h:i A') }}</td>
                        <td class="px-3 py-2 align-top">
                            <div class="font-medium">{{ $b->full_name }}</div>
                            <div class="text-gray-600">{{ $b->email }}</div>
                            @if($b->phone)
                                <div class="text-gray-600">{{ $b->phone }}</div>
                            @endif
                        </td>
                        <td class="px-3 py-2 align-top">
                            A: {{ $b->adult }} / C: {{ $b->child }} / I: {{ $b->infant }} / PWD: {{ $b->pwd }}
                        </td>
                        <td class="px-3 py-2 align-top">
                            <span class="inline-block px-2 py-1 rounded text-xs
                                @class([
                                    'bg-yellow-100 text-yellow-800' => $b->status==='pending',
                                    'bg-green-100 text-green-800' => $b->status==='confirmed',
                                    'bg-red-100 text-red-800' => $b->status==='cancelled',
                                ])
                            ">{{ ucfirst($b->status) }}</span>
                        </td>
                        <td class="px-3 py-2 align-top">₱{{ number_format($b->total_amount, 2) }}</td>
                        <td class="px-3 py-2 align-top">
                            <form action="{{ route('bookings.updateStatus', $b) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="border rounded p-1 text-sm">
                                    <option value="pending" @selected($b->status==='pending')>Pending</option>
                                    <option value="confirmed" @selected($b->status==='confirmed')>Confirmed</option>
                                    <option value="cancelled" @selected($b->status==='cancelled')>Cancelled</option>
                                </select>
                                <button class="ml-2 px-2 py-1 bg-blue-600 text-white rounded text-sm">Update</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-3 py-8 text-center text-gray-600">No bookings found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $bookings->links() }}</div>
</div>
@endsection