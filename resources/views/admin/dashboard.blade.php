@extends('layouts.superadmin')

@section('content')
<div class="space-y-6">
    <!-- Security Alerts -->
    @if(isset($twoFactorWarning) && $twoFactorWarning)
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm text-yellow-700">
                    <strong>Security Warning:</strong> Two-Factor Authentication is disabled. 
                    <a href="{{ route('profile.edit') }}" class="font-medium underline hover:text-yellow-600">Enable it now</a> for better account protection.
                </p>
            </div>
        </div>
    </div>
    @endif

    @if(isset($securityAlerts) && count($securityAlerts) > 0)
        @foreach($securityAlerts as $alert)
        <div class="bg-{{ $alert['type'] === 'warning' ? 'yellow' : 'blue' }}-50 border-l-4 border-{{ $alert['type'] === 'warning' ? 'yellow' : 'blue' }}-400 p-4 rounded-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-{{ $alert['type'] === 'warning' ? 'yellow' : 'blue' }}-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-{{ $alert['type'] === 'warning' ? 'yellow' : 'blue' }}-700">{{ $alert['message'] }}</p>
                    </div>
                </div>
                @if($alert['action_url'])
                <a href="{{ $alert['action_url'] }}" class="text-sm font-medium text-{{ $alert['type'] === 'warning' ? 'yellow' : 'blue' }}-700 hover:text-{{ $alert['type'] === 'warning' ? 'yellow' : 'blue' }}-600 underline">
                    {{ $alert['action_text'] }}
                </a>
                @endif
            </div>
        </div>
        @endforeach
    @endif

    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-gray-600">Here's what's happening with your ferry service.</p>
        </div>
        <div class="text-sm text-gray-500">
            Last updated: {{ now()->format('M d, Y h:i A') }}
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Today's Bookings -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Today's Bookings</p>
                    <p class="text-3xl font-bold text-blue-600">{{ \App\Models\Booking::whereDate('created_at', today())->count() }}</p>
                    <p class="text-sm text-gray-500">{{ \App\Models\Booking::whereDate('created_at', today())->sum(\DB::raw('adult + child + infant + pwd')) }} passengers</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Today's Revenue -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Today's Revenue</p>
                    <p class="text-3xl font-bold text-green-600">₱{{ number_format(\App\Models\Booking::whereDate('created_at', today())->where('status', 'confirmed')->sum('total_amount'), 2) }}</p>
                    <p class="text-sm text-gray-500">Confirmed bookings only</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Users -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Users</p>
                    <p class="text-3xl font-bold text-purple-600">{{ \App\Models\User::count() }}</p>
                    <p class="text-sm text-gray-500">Registered accounts</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Active Trips -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Trips</p>
                    <p class="text-3xl font-bold text-orange-600">{{ \App\Models\Trip::where('departure_time', '>=', now())->count() }}</p>
                    <p class="text-sm text-gray-500">Upcoming departures</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('bookings.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Manage Bookings</p>
                    <p class="text-sm text-gray-500">View and update bookings</p>
                </div>
            </a>

            <a href="{{ route('trips.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Manage Trips</p>
                    <p class="text-sm text-gray-500">Schedule and update trips</p>
                </div>
            </a>

            <a href="{{ route('reports.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-gray-900">View Reports</p>
                    <p class="text-sm text-gray-500">Analytics and insights</p>
                </div>
            </a>

            <a href="{{ route('users.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Manage Users</p>
                    <p class="text-sm text-gray-500">User accounts and roles</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Bookings -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Recent Bookings</h3>
                <a href="{{ route('bookings.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
            </div>
            <div class="space-y-3">
                @php
                    $recentBookings = \App\Models\Booking::with('trip')->latest()->limit(5)->get();
                @endphp
                @forelse($recentBookings as $booking)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">#{{ $booking->id }} - {{ $booking->full_name }}</p>
                        <p class="text-sm text-gray-500">{{ $booking->origin }} → {{ $booking->destination }}</p>
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
                @empty
                <p class="text-gray-500 text-center py-4">No recent bookings</p>
                @endforelse
            </div>
        </div>

        <!-- System Status -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">System Status</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-gray-700">Booking System</span>
                    </div>
                    <span class="text-sm text-green-600 font-medium">Online</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-gray-700">Payment Processing</span>
                    </div>
                    <span class="text-sm text-green-600 font-medium">Online</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="text-gray-700">Database</span>
                    </div>
                    <span class="text-sm text-green-600 font-medium">Online</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                        <span class="text-gray-700">Last Backup</span>
                    </div>
                    <span class="text-sm text-gray-600">{{ now()->subHours(2)->diffForHumans() }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Metrics (Super Admin Only) -->
    @if(auth()->user()->isSuperAdmin())
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Security Overview</h3>
            <a href="{{ route('admin.security.overview') }}" class="text-sm text-blue-600 hover:text-blue-800">View Details</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 bg-red-50 rounded-lg border border-red-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-red-600">Locked Accounts</p>
                        <p class="text-2xl font-bold text-red-700">{{ $lockedAccountsCount ?? 0 }}</p>
                    </div>
                    <svg class="w-8 h-8 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
            <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-yellow-600">Failed Logins (1h)</p>
                        <p class="text-2xl font-bold text-yellow-700">{{ $recentFailedLogins ?? 0 }}</p>
                    </div>
                    <svg class="w-8 h-8 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
            <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-600">2FA Enabled</p>
                        <p class="text-2xl font-bold text-green-700">{{ \App\Models\User::where('two_factor_enabled', true)->count() }}</p>
                    </div>
                    <svg class="w-8 h-8 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Session Timeout Warning (JavaScript) -->
<script>
    // Warn user 5 minutes before session expires
    const sessionLifetime = {{ config('session.lifetime') }} * 60 * 1000; // Convert to milliseconds
    const warningTime = sessionLifetime - (5 * 60 * 1000); // 5 minutes before expiry

    setTimeout(function() {
        if (confirm('Your session will expire in 5 minutes. Click OK to stay logged in.')) {
            // Ping server to keep session alive
            fetch('{{ route("dashboard") }}', {
                method: 'HEAD',
                credentials: 'same-origin'
            });
        }
    }, warningTime);
</script>
@endsection
