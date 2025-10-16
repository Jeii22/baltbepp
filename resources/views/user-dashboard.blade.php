@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-900 mb-4">My Dashboard</h1>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Profile Information -->
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Profile Information</h2>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Name</label>
                                <p class="mt-1 text-sm text-gray-900">{{ auth()->user()->display_name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email</label>
                                <p class="mt-1 text-sm text-gray-900">{{ auth()->user()->email }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Role</label>
                                <p class="mt-1 text-sm text-gray-900">{{ auth()->user()->getRoleDisplayName() }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Member Since</label>
                                <p class="mt-1 text-sm text-gray-900">{{ auth()->user()->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Edit Profile
                            </a>
                        </div>
                    </div>

                    <!-- Recent Bookings -->
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Bookings</h2>
                        <div class="space-y-3">
                            @php
                                $recentBookings = \App\Models\Booking::where('user_id', auth()->id())
                                    ->with('trip')
                                    ->latest()
                                    ->limit(5)
                                    ->get();
                            @endphp
                            @forelse($recentBookings as $booking)
                            <div class="bg-white p-4 rounded-lg shadow-sm border">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium text-gray-900">#{{ $booking->id }} - {{ $booking->full_name }}</p>
                                        <p class="text-sm text-gray-600">{{ $booking->origin }} → {{ $booking->destination }}</p>
                                        <p class="text-sm text-gray-500">{{ $booking->trip->departure_time->format('M d, Y H:i') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-medium text-gray-900">₱{{ number_format($booking->total_amount, 2) }}</p>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full
                                            @if($booking->status === 'confirmed') bg-green-100 text-green-800
                                            @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <p class="text-gray-500 text-center py-4">No recent bookings</p>
                            @endforelse
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('welcome') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Book a Trip
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection