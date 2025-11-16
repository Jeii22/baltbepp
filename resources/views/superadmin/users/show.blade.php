@extends('layouts.superadmin')

@section('content')
<div class="mb-6">
    <a href="{{ route('users.index') }}" class="text-blue-600 hover:text-blue-800">&larr; Back to Users</a>
    @if(auth()->user() && auth()->user()->role === 'super_admin')
        <a href="{{ route('admin.users.logs', $user->id) }}" class="ml-4 px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-900 transition">View Logs</a>
    @endif
</div>

<div class="bg-white shadow rounded-xl p-6 mb-6">
    <h1 class="text-2xl font-bold mb-4">{{ $user->display_name }}</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <strong>Email:</strong> {{ $user->email }}
        </div>
        <div>
            <strong>Username:</strong> {{ $user->username }}
        </div>
        <div>
            <strong>Role:</strong> {{ ucfirst(str_replace('_', ' ', $user->role)) }}
        </div>
        <div>
            <strong>Last Active:</strong> {{ $user->last_active_at ? $user->last_active_at->diffForHumans() : 'Never' }}
        </div>
        <div>
            <strong>Total Attempts:</strong> {{ $totalAttempts }}
        </div>
        <div>
            <strong>Failed Attempts:</strong> {{ $failedAttempts }}
        </div>
    </div>
</div>

<div class="bg-white shadow rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
        {{-- Login Attempts Table --}}
        <div>
            <h2 class="text-xl font-bold mb-4">Login Attempts</h2>
            <table id="loginAttemptsTable" class="min-w-full bg-white rounded shadow">
                <thead>
                    <tr class="bg-gray-100 text-gray-700">
                        <th class="px-4 py-2 text-left">Date</th>
                        <th class="px-4 py-2 text-left">IP Address</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($loginAttempts as $attempt)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $attempt->created_at }}</td>
                            <td class="px-4 py-2">{{ $attempt->ip_address }}</td>
                            <td class="px-4 py-2">
                                <span class="inline-block px-2 py-1 rounded 
                                    {{ $attempt->successful ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $attempt->successful ? 'Success' : 'Failed' }}
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                <button 
                                    class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs transition
                                        {{ empty($attempt->latitude) || empty($attempt->longitude) ? 'bg-gray-300 cursor-not-allowed' : '' }}"
                                    onclick="showLocationModal(
                                        '{{ $attempt->latitude }}', 
                                        '{{ $attempt->longitude }}', 
                                        '{{ $attempt->city ?? '' }}', 
                                        '{{ $attempt->region ?? '' }}', 
                                        '{{ $attempt->country ?? '' }}', 
                                        '{{ $attempt->created_at }}', 
                                        '{{ $attempt->ip_address }}'
                                    )"
                                    {{ empty($attempt->latitude) || empty($attempt->longitude) ? 'disabled' : '' }}
                                >
                                    View Location
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $loginAttempts->links() }}
    </div>
</div>

<!-- Location Modal -->
<div id="locationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-4xl relative max-h-[90vh] overflow-y-auto">
        <button onclick="closeLocationModal()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl font-bold z-10 bg-white rounded-full w-8 h-8 flex items-center justify-center shadow-lg">&times;</button>
        
        <h2 class="text-2xl font-semibold mb-4 text-gray-900">Login Attempt Location</h2>
        
        <!-- Location Details Card -->
        <div id="locationDetails" class="mb-6 bg-gradient-to-r from-blue-50 to-cyan-50 p-6 rounded-xl border border-blue-100"></div>
        
        <!-- Map Tabs -->
        <div class="mb-4">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <button onclick="switchMapProvider('google')" id="googleTab" class="map-tab border-b-2 border-blue-500 py-2 px-1 text-sm font-medium text-blue-600">
                        Google Maps
                    </button>
                    <button onclick="switchMapProvider('osm')" id="osmTab" class="map-tab border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        OpenStreetMap
                    </button>
                </nav>
            </div>
        </div>
        
        <!-- Map Container -->
        <div class="relative">
            <div id="mapLoading" class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10 rounded-lg">
                <div class="text-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-2"></div>
                    <p class="text-gray-600">Loading map...</p>
                </div>
            </div>
            
            <!-- Google Maps -->
            <iframe id="googleMap" class="map-frame w-full h-96 rounded-lg shadow-lg border-2 border-gray-200" frameborder="0" allowfullscreen></iframe>
            
            <!-- OpenStreetMap -->
            <iframe id="osmMap" class="map-frame w-full h-96 rounded-lg shadow-lg border-2 border-gray-200 hidden" frameborder="0" allowfullscreen></iframe>
            
            <!-- Error Message -->
            <div id="mapError" class="hidden bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                <svg class="w-12 h-12 text-red-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <p class="text-red-600 font-medium">Unable to load map</p>
                <p class="text-red-500 text-sm mt-1">Location data may be unavailable for this login attempt.</p>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="mt-6 flex gap-3 justify-end">
            <a id="googleMapsLink" href="#" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Open in Google Maps
            </a>
            <button onclick="closeLocationModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                Close
            </button>
        </div>
    </div>
</div>

<script>
let currentProvider = 'google';
let currentLat = null;
let currentLng = null;

function showLocationModal(lat, lng, city, region, country, date, ip) {
    if (!lat || !lng || lat === 'null' || lng === 'null' || lat === '' || lng === '') {
        alert('Location data is not available for this login attempt.');
        return;
    }
    
    currentLat = parseFloat(lat);
    currentLng = parseFloat(lng);
    
    if (isNaN(currentLat) || isNaN(currentLng)) {
        alert('Invalid location coordinates.');
        return;
    }
    
    const modal = document.getElementById('locationModal');
    const details = document.getElementById('locationDetails');
    const googleMapsLink = document.getElementById('googleMapsLink');
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Format location info
    const locationParts = [];
    if (city && city !== 'Unknown') locationParts.push(city);
    if (region && region !== 'Unknown') locationParts.push(region);
    if (country && country !== 'Unknown') locationParts.push(country);
    const locationStr = locationParts.length > 0 ? locationParts.join(', ') : 'Unknown Location';
    
    details.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white p-3 rounded-lg shadow-sm">
                <div class="text-xs text-gray-500 mb-1">Date & Time</div>
                <div class="font-medium text-gray-900">${date}</div>
            </div>
            <div class="bg-white p-3 rounded-lg shadow-sm">
                <div class="text-xs text-gray-500 mb-1">IP Address</div>
                <div class="font-medium text-gray-900">${ip}</div>
            </div>
            <div class="bg-white p-3 rounded-lg shadow-sm">
                <div class="text-xs text-gray-500 mb-1">Location</div>
                <div class="font-medium text-gray-900">${locationStr}</div>
            </div>
            <div class="bg-white p-3 rounded-lg shadow-sm md:col-span-3">
                <div class="text-xs text-gray-500 mb-1">Coordinates</div>
                <div class="font-medium text-gray-900">
                    Latitude: ${currentLat.toFixed(6)}°, Longitude: ${currentLng.toFixed(6)}°
                </div>
            </div>
        </div>
    `;
    
    // Set Google Maps external link
    googleMapsLink.href = `https://www.google.com/maps?q=${currentLat},${currentLng}`;
    
    // Load map
    switchMapProvider('google');
}

function switchMapProvider(provider) {
    if (!currentLat || !currentLng) return;
    
    currentProvider = provider;
    const googleMap = document.getElementById('googleMap');
    const osmMap = document.getElementById('osmMap');
    const googleTab = document.getElementById('googleTab');
    const osmTab = document.getElementById('osmTab');
    const mapLoading = document.getElementById('mapLoading');
    const mapError = document.getElementById('mapError');
    
    // Show loading
    mapLoading.classList.remove('hidden');
    mapError.classList.add('hidden');
    
    // Update tabs
    googleTab.className = provider === 'google' 
        ? 'map-tab border-b-2 border-blue-500 py-2 px-1 text-sm font-medium text-blue-600'
        : 'map-tab border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300';
    
    osmTab.className = provider === 'osm'
        ? 'map-tab border-b-2 border-blue-500 py-2 px-1 text-sm font-medium text-blue-600'
        : 'map-tab border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300';
    
    if (provider === 'google') {
        googleMap.classList.remove('hidden');
        osmMap.classList.add('hidden');
        googleMap.src = `https://maps.google.com/maps?q=${currentLat},${currentLng}&z=15&output=embed`;
        
        // Hide loading after a short delay
        setTimeout(() => mapLoading.classList.add('hidden'), 1500);
    } else {
        osmMap.classList.remove('hidden');
        googleMap.classList.add('hidden');
        osmMap.src = `https://www.openstreetmap.org/export/embed.html?bbox=${currentLng-0.01},${currentLat-0.01},${currentLng+0.01},${currentLat+0.01}&layer=mapnik&marker=${currentLat},${currentLng}`;
        
        // Hide loading after a short delay
        setTimeout(() => mapLoading.classList.add('hidden'), 1500);
    }
}

function closeLocationModal() {
    const modal = document.getElementById('locationModal');
    const googleMap = document.getElementById('googleMap');
    const osmMap = document.getElementById('osmMap');
    
    modal.classList.add('hidden');
    document.body.style.overflow = '';
    
    // Clear map sources to stop loading
    googleMap.src = '';
    osmMap.src = '';
    
    currentLat = null;
    currentLng = null;
}

// Close modal when clicking outside
document.getElementById('locationModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeLocationModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeLocationModal();
    }
});
</script>
@endsection