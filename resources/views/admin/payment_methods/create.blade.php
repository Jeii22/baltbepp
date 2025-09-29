@extends('layouts.superadmin')

@section('content')
<div class="p-6">
    <div class="max-w-xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Add Payment Method</h1>

        <form action="{{ route('admin.payment-methods.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 bg-white p-6 border rounded shadow-sm">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">Type</label>
                <select name="type" class="w-full border rounded px-3 py-2">
                    <option value="gcash">GCash</option>
                    <option value="paymaya">Maya</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Label</label>
                <input type="text" name="label" class="w-full border rounded px-3 py-2" placeholder="e.g., Main GCash Wallet" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Account Name</label>
                <input type="text" name="account_name" class="w-full border rounded px-3 py-2" placeholder="e.g., BaltBep Transport Corp">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Account Number (Phone / Wallet ID)</label>
                <input type="text" name="account_number" class="w-full border rounded px-3 py-2" placeholder="09xxxxxxxxx" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">QR Code Screenshot</label>
                <input type="file" name="qr_code_image" class="w-full border rounded px-3 py-2" accept="image/*">
                <p class="text-xs text-gray-500 mt-1">Upload a screenshot of the QR code for easy scanning. Max 2MB, JPEG/PNG/GIF.</p>
            </div>
            <div class="flex items-center space-x-2">
                <input type="checkbox" name="is_active" value="1" checked>
                <span>Active</span>
            </div>
            <div class="flex gap-3 pt-2">
                <a href="{{ route('admin.payment-methods.index') }}" class="px-4 py-2 border rounded">Cancel</a>
                <button class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection