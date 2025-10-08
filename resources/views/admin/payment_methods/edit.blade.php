@extends('layouts.superadmin')

@section('content')
<div class="p-6">
    <div class="max-w-xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Edit Payment Method</h1>

        <form action="{{ route('admin.payment-methods.update', $method) }}" method="POST" enctype="multipart/form-data" class="space-y-4 bg-white p-6 border rounded shadow-sm">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium mb-1">Type</label>
                <select name="type" class="w-full border rounded px-3 py-2">
                    <option value="gcash" @selected($method->type==='gcash')>GCash</option>
                    <option value="paymaya" @selected($method->type==='paymaya')>Maya</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Label</label>
                <input type="text" name="label" class="w-full border rounded px-3 py-2" value="{{ $method->label }}" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Account Name</label>
                <input type="text" name="account_name" class="w-full border rounded px-3 py-2" value="{{ $method->account_name }}">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Account Number (Phone / Wallet ID)</label>
                <input type="text" name="account_number" class="w-full border rounded px-3 py-2" value="{{ $method->account_number }}" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">QR Code Screenshot</label>
                @if($method->qr_code_image)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $method->qr_code_image) }}" alt="Current QR Code" class="w-24 h-24 object-cover border">
                        <p class="text-xs text-gray-500">Current QR Code</p>
                    </div>
                @endif
                <input type="file" name="qr_code_image" class="w-full border rounded px-3 py-2" accept="image/*">
                <p class="text-xs text-gray-500 mt-1">Upload a new screenshot if needed. Max 2MB, JPEG/PNG/GIF. Leave blank to keep current.</p>
            </div>
            <div class="flex items-center space-x-2">
                <input type="checkbox" name="is_active" value="1" @checked($method->is_active)>
                <span>Active</span>
            </div>
            <div class="flex gap-3 pt-2">
                <a href="{{ route('admin.payment-methods.index') }}" class="px-4 py-2 border rounded">Cancel</a>
                <button class="px-4 py-2 bg-blue-600 text-white rounded">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection