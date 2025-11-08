@extends('layouts.superadmin')

@section('content')
<div class="mb-6">
    <a href="{{ route('users.index') }}" class="text-blue-600 hover:text-blue-800">&larr; Back to Users</a>
    <a href="{{ route('admin.users.logs', $user->id) }}" class="ml-4 text-blue-600 hover:text-blue-800">Refresh Logs</a>
</div>

<div class="bg-white shadow rounded-xl p-6 mb-6">
    <h1 class="text-2xl font-bold mb-4">Activity Logs for {{ $user->display_name }}</h1>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded shadow">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="px-4 py-2 text-left">Date</th>
                    <th class="px-4 py-2 text-left">Action</th>
                    <th class="px-4 py-2 text-left">Description</th>
                    <th class="px-4 py-2 text-left">IP Address</th>
                    <th class="px-4 py-2 text-left">User Agent</th>
                    <th class="px-4 py-2 text-left">Metadata</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $log->created_at }}</td>
                        <td class="px-4 py-2">{{ $log->action }}</td>
                        <td class="px-4 py-2">{{ $log->description }}</td>
                        <td class="px-4 py-2">{{ $log->ip_address }}</td>
                        <td class="px-4 py-2 text-xs">{{ Str::limit($log->user_agent, 40) }}</td>
                        <td class="px-4 py-2 text-xs">
                            @if($log->metadata)
                                <pre class="whitespace-pre-wrap text-xs">{{ json_encode(json_decode($log->metadata), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-2 text-center text-gray-500">No activity logs found for this user.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">{{ $logs->links() }}</div>
    </div>
</div>
@endsection
