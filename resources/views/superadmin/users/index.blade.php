@extends('layouts.superadmin')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Admin Management</h1>
    <a href="{{ route('users.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Admin</a>
</div>

<div class="bg-white shadow rounded-xl overflow-hidden">
    <table class="min-w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Name</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Email</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Username</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Role</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Last Active</th>
                <th class="px-4 py-3 text-right text-sm font-semibold text-gray-600">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($admins as $admin)
                <tr>
                    <td class="px-4 py-3">{{ $admin->display_name }}</td>
                    <td class="px-4 py-3">{{ $admin->email }}</td>
                    <td class="px-4 py-3">{{ $admin->username }}</td>
                    <td class="px-4 py-3"><span class="inline-block px-2 py-1 text-xs rounded bg-blue-100 text-blue-700">{{ ucfirst(str_replace('_',' ', $admin->role)) }}</span></td>
                    <td class="px-4 py-3">{{ $admin->last_active_at ? $admin->last_active_at->diffForHumans() : '—' }}</td>
                    <td class="px-4 py-3 text-right">
                        <form method="POST" action="{{ route('users.destroy', $admin) }}" onsubmit="return confirm('Delete this user?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 hover:text-red-800">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">No admins yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $admins->links() }}
</div>
@endsection