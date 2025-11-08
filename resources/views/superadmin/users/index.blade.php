@extends('layouts.superadmin')

@section('content')
<div class="flex items-center justify-between mb-6 print-container">
    <h1 class="text-2xl font-bold">User Management</h1>
    <a href="{{ route('users.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 print-hidden">Add Admin</a>
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
            @forelse ($users as $user)
                <tr>
                    <td class="px-4 py-3">{{ $user->display_name }}</td>
                    <td class="px-4 py-3">{{ $user->email }}</td>
                    <td class="px-4 py-3">{{ $user->username }}</td>
                    <td class="px-4 py-3"><span class="inline-block px-2 py-1 text-xs rounded bg-blue-100 text-blue-700">{{ ucfirst(str_replace('_',' ', $user->role)) }}</span></td>
                    <td class="px-4 py-3">{{ $user->last_active_at ? $user->last_active_at->diffForHumans() : '—' }}</td>
                    <td class="px-4 py-3 text-right space-x-2">
                        <a href="{{ route('users.show', $user) }}" class="inline-flex items-center px-3 py-1 text-sm font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-md transition-colors" title="View Login Attempts">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                            View
                        </a>
                        @if($user->role !== 'super_admin')
                        <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Are you sure you want to delete this user?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-3 py-1 text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-md transition-colors" title="Delete User">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                                Delete
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">No users yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4 print-hidden">
    {{ $users->links() }}
</div>

<style>
@media print {
    /* Hide interactive elements */
    .print-hidden,
    button,
    input,
    select,
    textarea,
    form,
    a[href]:not([href^="#"]) {
        display: none !important;
    }

    /* Page setup */
    body {
        font-family: 'Times New Roman', Times, serif;
        font-size: 12pt;
        line-height: 1.4;
        color: #000;
        background: #fff !important;
        margin: 0;
        padding: 0;
    }

    /* Container styling */
    .print-container {
        max-width: none;
        margin: 0;
        padding: 20pt;
        box-shadow: none;
        border: none;
    }

    /* Typography */
    h1, h2, h3, h4, h5, h6 {
        color: #000;
        page-break-after: avoid;
        margin-top: 0;
    }

    h1 {
        font-size: 18pt;
        font-weight: bold;
        margin-bottom: 15pt;
        text-align: center;
    }

    /* Table styling */
    table {
        width: 100%;
        border-collapse: collapse;
        margin: 10pt 0;
        font-size: 10pt;
    }

    th, td {
        border: 1pt solid #000;
        padding: 6pt;
        text-align: left;
        vertical-align: top;
    }

    th {
        background: #f5f5f5 !important;
        font-weight: bold;
        -webkit-print-color-adjust: exact;
        color-adjust: exact;
    }

    tr {
        page-break-inside: avoid;
    }

    /* Role badges */
    .print-role-badge {
        display: inline-block;
        padding: 2pt 4pt;
        font-size: 9pt;
        font-weight: bold;
        border: 1pt solid #000;
        background: #fff !important;
        -webkit-print-color-adjust: exact;
        color-adjust: exact;
    }

    /* Status indicators */
    .print-status-active {
        font-weight: bold;
        color: #000;
    }

    .print-status-inactive {
        font-weight: bold;
        color: #666;
    }

    /* Layout utilities */
    .print-grid {
        display: table;
        width: 100%;
    }

    .print-row {
        display: table-row;
    }

    .print-cell {
        display: table-cell;
        padding: 4pt;
    }

    /* Spacing */
    .print-section {
        margin-bottom: 15pt;
        page-break-inside: avoid;
    }

    /* Hide layout elements */
    .flex,
    .items-center,
    .justify-between,
    .mb-6,
    .bg-white,
    .shadow,
    .rounded-xl,
    .overflow-hidden,
    .min-w-full,
    .bg-gray-50,
    .divide-y,
    .divide-gray-100,
    .mt-4 {
        all: unset;
    }

    /* Ensure content flows properly */
    .print-flow {
        display: block;
        width: 100%;
    }

    /* Page breaks */
    .page-break {
        page-break-before: always;
    }

    .no-break {
        page-break-inside: avoid;
    }

    /* SVG icons */
    svg {
        display: none !important;
    }
}
</style>
@endsection