@extends('layouts.superadmin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Reports & Analytics</h1>
            <p class="text-gray-600">Comprehensive business insights and data analysis</p>
        </div>
        
        <!-- Date Range Filter -->
        <div class="flex items-center space-x-4">
            <form method="GET" action="{{ route('reports.index') }}" class="flex items-center space-x-2">
                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-gray-700">From:</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" 
                           class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-gray-700">To:</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" 
                           class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                    Apply Filter
                </button>
            </form>
        </div>
    </div>

    <!-- Overview Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Bookings -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Bookings</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_bookings']) }}</p>
                    @if($stats['bookings_growth'] != 0)
                        <p class="text-sm {{ $stats['bookings_growth'] > 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $stats['bookings_growth'] > 0 ? '+' : '' }}{{ number_format($stats['bookings_growth'], 1) }}% from previous period
                        </p>
                    @endif
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                    <p class="text-3xl font-bold text-gray-900">₱{{ number_format($stats['total_revenue'], 2) }}</p>
                    @if($stats['revenue_growth'] != 0)
                        <p class="text-sm {{ $stats['revenue_growth'] > 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $stats['revenue_growth'] > 0 ? '+' : '' }}{{ number_format($stats['revenue_growth'], 1) }}% from previous period
                        </p>
                    @endif
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Passengers -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Passengers</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_passengers']) }}</p>
                    <p class="text-sm text-gray-500">Across all bookings</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Average Booking Value -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Avg. Booking Value</p>
                    <p class="text-3xl font-bold text-gray-900">₱{{ number_format($stats['average_booking_value'], 2) }}</p>
                    <p class="text-sm text-gray-500">Per booking</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Booking Trends Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Booking Trends</h3>
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                    <span class="text-sm text-gray-600">Daily Bookings</span>
                </div>
            </div>
            <div class="h-64">
                <canvas id="bookingTrendsChart"></canvas>
            </div>
        </div>

        <!-- Revenue Trends Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Revenue Trends</h3>
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                    <span class="text-sm text-gray-600">Daily Revenue</span>
                </div>
            </div>
            <div class="h-64">
                <canvas id="revenueTrendsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Data Tables Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Popular Routes -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Popular Routes</h3>
                <span class="text-sm text-gray-500">Top 10 routes by bookings</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Route</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bookings</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($popularRoutes as $route)
                        <tr>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $route->route }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ number_format($route->bookings) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">₱{{ number_format($route->revenue, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-sm text-gray-500 text-center">No data available</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Payment Methods</h3>
                <span class="text-sm text-gray-500">Breakdown by method</span>
            </div>
            <div class="space-y-4">
                @forelse($paymentMethods as $method)
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 rounded-full 
                            @if($method->payment_method === 'cod') bg-orange-500
                            @elseif($method->payment_method === 'gcash') bg-blue-500
                            @elseif($method->payment_method === 'paymaya') bg-green-500
                            @elseif($method->payment_method === 'card') bg-purple-500
                            @else bg-gray-500 @endif">
                        </div>
                        <span class="text-sm font-medium text-gray-900">
                            @switch($method->payment_method)
                                @case('cod') Cash on Departure @break
                                @case('gcash') GCash @break
                                @case('paymaya') PayMaya @break
                                @case('card') Credit/Debit Card @break
                                @default {{ ucfirst($method->payment_method ?? 'Unknown') }}
                            @endswitch
                        </span>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-medium text-gray-900">{{ number_format($method->count) }} bookings</div>
                        <div class="text-xs text-gray-500">₱{{ number_format($method->revenue, 2) }}</div>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-500 text-center">No payment data available</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Recent Bookings</h3>
            <a href="{{ route('bookings.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Route</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Passenger</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentBookings as $booking)
                    <tr>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">#{{ $booking->id }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $booking->created_at->format('M d, Y') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $booking->origin }} → {{ $booking->destination }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $booking->full_name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">₱{{ number_format($booking->total_amount, 2) }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                @if($booking->status === 'confirmed') bg-green-100 text-green-800
                                @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-3 text-sm text-gray-500 text-center">No recent bookings</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Export Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Export Data</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Export Bookings -->
            <div class="border border-gray-200 rounded-lg p-4">
                <h4 class="font-medium text-gray-900 mb-2">Bookings Report</h4>
                <p class="text-sm text-gray-600 mb-3">Export detailed booking information including passenger details and payment methods.</p>
                <a href="{{ route('reports.export', ['type' => 'bookings', 'format' => 'csv', 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
                   class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                    Download CSV
                </a>
            </div>

            <!-- Export Revenue -->
            <div class="border border-gray-200 rounded-lg p-4">
                <h4 class="font-medium text-gray-900 mb-2">Revenue Report</h4>
                <p class="text-sm text-gray-600 mb-3">Export daily revenue data and booking counts for financial analysis.</p>
                <a href="{{ route('reports.export', ['type' => 'revenue', 'format' => 'csv', 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
                   class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                    Download CSV
                </a>
            </div>

            <!-- Export Passengers -->
            <div class="border border-gray-200 rounded-lg p-4">
                <h4 class="font-medium text-gray-900 mb-2">Passenger Report</h4>
                <p class="text-sm text-gray-600 mb-3">Export passenger statistics broken down by category and date.</p>
                <a href="{{ route('reports.export', ['type' => 'passengers', 'format' => 'csv', 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
                   class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                    Download CSV
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Booking Trends Chart
    const bookingCtx = document.getElementById('bookingTrendsChart').getContext('2d');
    const bookingData = @json($bookingTrends);
    
    new Chart(bookingCtx, {
        type: 'line',
        data: {
            labels: bookingData.map(item => new Date(item.date).toLocaleDateString()),
            datasets: [{
                label: 'Daily Bookings',
                data: bookingData.map(item => item.count),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Revenue Trends Chart
    const revenueCtx = document.getElementById('revenueTrendsChart').getContext('2d');
    const revenueData = @json($revenueTrends);
    
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: revenueData.map(item => new Date(item.date).toLocaleDateString()),
            datasets: [{
                label: 'Daily Revenue',
                data: revenueData.map(item => parseFloat(item.revenue)),
                borderColor: 'rgb(34, 197, 94)',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Revenue: ₱' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection