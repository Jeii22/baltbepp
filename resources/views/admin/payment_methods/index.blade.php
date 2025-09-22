@extends('layouts.superadmin')

@section('content')
<div class="p-6">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Payment Methods</h1>
            <a href="{{ route('admin.payment-methods.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Add Method</a>
        </div>

        <div class="mb-6 bg-white border rounded p-4 flex items-center justify-between">
            <div>
                <div class="font-medium">PayMongo</div>
                <div class="text-sm text-gray-600">Toggle to activate or deactivate PayMongo across checkout.</div>
            </div>
            <form method="POST" action="{{ route('settings.update') }}" class="flex items-center">
                @csrf
                @method('PUT')
                <input type="hidden" name="cod_enabled" value="{{ $codEnabled ? 'on' : '' }}">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="paymongo_enabled" class="sr-only" {{ $paymongoEnabled ? 'checked' : '' }} onchange="this.form.submit()">
                    <span class="w-11 h-6 rounded-full relative transition {{ $paymongoEnabled ? 'bg-green-500' : 'bg-red-500' }}">
                        <span class="dot absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition transform {{ $paymongoEnabled ? 'translate-x-5' : '' }}"></span>
                    </span>
                </label>
            </form>
        </div>



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
                                <form action="{{ route('admin.payment-methods.destroy', $method) }}" method="POST" class="inline" data-confirm="Delete this method?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1 border rounded text-red-600">Delete</button>
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