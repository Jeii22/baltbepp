@extends('layouts.superadmin')

@section('content')
<div class="p-6">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Payment Methods</h1>
            <a href="{{ route('admin.payment-methods.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Add Method</a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-800 rounded">{{ session('success') }}</div>
        @endif

        <div class="bg-white border rounded shadow-sm">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left p-3">Type</th>
                        <th class="text-left p-3">Label</th>
                        <th class="text-left p-3">Account Name</th>
                        <th class="text-left p-3">Account #</th>
                        <th class="text-left p-3">Active</th>
                        <th class="text-right p-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($methods as $method)
                        <tr class="border-t">
                            <td class="p-3 uppercase">{{ $method->type }}</td>
                            <td class="p-3">{{ $method->label }}</td>
                            <td class="p-3">{{ $method->account_name }}</td>
                            <td class="p-3">{{ $method->account_number }}</td>
                            <td class="p-3">{{ $method->is_active ? 'Yes' : 'No' }}</td>
                            <td class="p-3 text-right space-x-2">
                                <a class="px-3 py-1 border rounded" href="{{ route('admin.payment-methods.edit', $method) }}">Edit</a>
                                <form action="{{ route('admin.payment-methods.destroy', $method) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-3 py-1 border rounded text-red-600" onclick="return confirm('Delete this method?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-6 text-center text-gray-500">No payment methods yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection