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
        
        {{-- Location Modal --}}
        <div id="locationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md relative">
                <button onclick="closeLocationModal()" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
                <h2 class="text-lg font-semibold mb-2">Login Attempt Location</h2>
                <div id="locationDetails" class="mb-4 text-sm text-gray-700"></div>
                <iframe id="locationMap" width="100%" height="250" frameborder="0" style="border:0" allowfullscreen></iframe>
            </div>
        </div>
        
        <script>
        function showLocationModal(lat, lng, city, region, country, date, ip) {
            if (!lat || !lng) return;
            document.getElementById('locationModal').classList.remove('hidden');
            document.getElementById('locationDetails').innerHTML = `
                <strong>Date:</strong> ${date}<br>
                <strong>IP Address:</strong> ${ip}<br>
                <strong>City:</strong> ${city || '-'}<br>
                <strong>Region:</strong> ${region || '-'}<br>
                <strong>Country:</strong> ${country || '-'}<br>
                <strong>Latitude:</strong> ${lat}<br>
                <strong>Longitude:</strong> ${lng}
            `;
            document.getElementById('locationMap').src = 
                `https://maps.google.com/maps?q=${lat},${lng}&z=15&output=embed`;
        }
        function closeLocationModal() {
            document.getElementById('locationModal').classList.add('hidden');
            document.getElementById('locationMap').src = '';
        }
        </script>
    </div>
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $loginAttempts->links() }}
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const showFailedBtn = document.getElementById('showFailedBtn');
        const showAllBtn = document.getElementById('showAllBtn');
        const rows = document.querySelectorAll('.login-attempt-row');

        showFailedBtn.addEventListener('click', function() {
            rows.forEach(row => {
                if (!row.classList.contains('failed')) {
                    row.style.display = 'none';
                } else {
                    row.style.display = '';
                }
            });
            showFailedBtn.style.display = 'none';
            showAllBtn.style.display = '';
        });

        showAllBtn.addEventListener('click', function() {
            rows.forEach(row => {
                row.style.display = '';
            });
            showFailedBtn.style.display = '';
            showAllBtn.style.display = 'none';
        });
    });
</script>
<!-- Modal for displaying map -->
<div id="locationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md relative">
        <button onclick="closeLocationModal()" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        <h2 class="text-lg font-semibold mb-2">Login Attempt Location</h2>
        <div id="locationDetails" class="mb-4 text-sm text-gray-700"></div>
        <iframe id="locationMap" width="100%" height="250" frameborder="0" style="border:0" allowfullscreen></iframe>
    </div>
</div>

<script>
function showLocationModal(lat, lng, city, region, country, date, ip) {
    if (!lat || !lng) return;
    document.getElementById('locationModal').classList.remove('hidden');
    document.getElementById('locationDetails').innerHTML = `
        <strong>Date:</strong> ${date}<br>
        <strong>IP Address:</strong> ${ip}<br>
        <strong>City:</strong> ${city || '-'}<br>
        <strong>Region:</strong> ${region || '-'}<br>
        <strong>Country:</strong> ${country || '-'}<br>
        <strong>Latitude:</strong> ${lat}<br>
        <strong>Longitude:</strong> ${lng}
    `;
    document.getElementById('locationMap').src = 
        `https://maps.google.com/maps?q=${lat},${lng}&z=15&output=embed`;
}
function closeLocationModal() {
    document.getElementById('locationModal').classList.add('hidden');
    document.getElementById('locationMap').src = '';
}
</script>
</script>
@endsection