@extends('layouts.superadmin')

@section('content')
<div class="mb-6">
    <a href="{{ route('users.index') }}" class="text-blue-600 hover:text-blue-800">&larr; Back to Users</a>
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
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-semibold">Login Attempts</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Date</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">IP Address</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Location</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">User Agent</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($loginAttempts as $attempt)
                    <tr>
                        <td class="px-4 py-3">{{ $attempt->attempted_at->format('M d, Y H:i') }}</td>
                        <td class="px-4 py-3">{{ $attempt->ip_address }}</td>
                        <td class="px-4 py-3">
                            @if($attempt->ip_address === '127.0.0.1' || $attempt->ip_address === '::1')
                                Localhost
                            @else
                                {{ $attempt->ip_address }}
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @if($attempt->user_agent)
                                <span title="{{ $attempt->user_agent }}">
                                    {{ Str::limit($attempt->user_agent, 30) }}
                                </span>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($attempt->successful)
                                <span class="inline-block px-2 py-1 text-xs rounded bg-green-100 text-green-700">Success</span>
                            @else
                                <span class="inline-block px-2 py-1 text-xs rounded bg-red-100 text-red-700">Failed</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">No login attempts recorded.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $loginAttempts->links() }}
    </div>
</div>
@endsection