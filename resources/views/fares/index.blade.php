@extends('layouts.superadmin')

@section('content')
<div class="p-6">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-semibold text-gray-900">Fare Management</h1>
            <p class="text-sm text-gray-500 mt-1">Manage pricing tiers for every passenger type across your fleet.</p>
        </div>
        <a href="{{ route('fares.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold shadow hover:from-blue-700 hover:to-indigo-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Add Fare
        </a>
    </div>

    <div class="bg-white border border-gray-100 shadow-sm rounded-2xl overflow-hidden">
        <div class="hidden lg:grid grid-cols-12 items-center bg-gray-50 border-b border-gray-100 px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">
            <span class="col-span-4">Passenger Type</span>
            <span class="col-span-2">Price</span>
            <span class="col-span-2">Status</span>
            <span class="col-span-4 text-right">Actions</span>
        </div>

        <div class="divide-y divide-gray-100">
            @forelse ($fares as $fare)
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 px-6 py-5 hover:bg-gray-50 transition">
                    <div class="lg:col-span-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-semibold">
                                {{ strtoupper(substr($fare->passenger_type, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $fare->passenger_type }}</p>
                                <p class="text-xs text-gray-500">Fare ID: {{ $fare->id }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-2">
                        <p class="text-lg font-semibold text-gray-900">₱{{ number_format($fare->price, 2) }}</p>
                        <p class="text-xs text-gray-500">Per ticket</p>
                    </div>

                    <div class="lg:col-span-2">
                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-medium {{ $fare->active ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }}">
                            <span class="w-2 h-2 rounded-full {{ $fare->active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                            {{ $fare->active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <div class="lg:col-span-4 flex lg:justify-end gap-3">
                        <a href="{{ route('fares.edit', $fare->id) }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-100 rounded-lg hover:bg-blue-100 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 7.125L16.875 4.5" />
                            </svg>
                            Edit
                        </a>
                        <form action="{{ route('fares.destroy', $fare->id) }}" method="POST" onsubmit="return confirm('Delete this fare?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-red-600 bg-red-50 border border-red-100 rounded-lg hover:bg-red-100 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.02.166m-1.02-.165L18.16 19.673A2.25 2.25 0 0115.917 21.75H8.083a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.338-.059.678-.114 1.02-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a51.964 51.964 0 00-7.5 0" />
                                </svg>
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center text-gray-500">
                    <div class="w-16 h-16 mx-auto rounded-full bg-gray-100 flex items-center justify-center text-gray-400 mb-4">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 001.5 1.5h4.5a1.5 1.5 0 001.5-1.5m-7.5 0V6.108a1.5 1.5 0 011.09-1.447l2.885-.752a1.5 1.5 0 01.75 0l2.885.752a1.5 1.5 0 011.09 1.447V18.75m-7.5 0h7.5" />
                        </svg>
                    </div>
                    <h3 class="text-base font-medium text-gray-800">No fares configured yet</h3>
                    <p class="text-sm text-gray-500 mt-1">Start by creating a fare to apply pricing rules for passengers.</p>
                    <a href="{{ route('fares.create') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition">
                        Create fare
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection