<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

use App\Traits\LogsAdminActivity;

class PaymentMethodController extends Controller
{
    use LogsAdminActivity;
    public function index()
    {
        $methods = PaymentMethod::orderBy('type')->get();
        $paymongoEnabled = \App\Models\Setting::getBool('paymongo_enabled', true);
        $codEnabled = \App\Models\Setting::getBool('cod_enabled', true);
        return view('admin.payment_methods.index', compact('methods', 'paymongoEnabled', 'codEnabled'));
    }

    public function create()
    {
        return view('admin.payment_methods.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:gcash,paymaya',
            'label' => 'required|string|max:120',
            'account_name' => 'nullable|string|max:120',
            'account_number' => 'required|string|max:50',
            'is_active' => 'sometimes|boolean',
            'qr_code_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('qr_code_image')) {
            // Ensure target directory exists: public/storage/payment_qr_codes
            $target = public_path('storage/payment_qr_codes');
            if (!File::exists($target)) {
                File::makeDirectory($target, 0755, true);
            }
            
            try {
                $file = $request->file('qr_code_image');
                $filename = $file->hashName();
                $file->storeAs('', $filename, 'payment_qr_codes');
                $data['qr_code_image'] = $filename;
            } catch (\Exception $e) {
                \Log::error('QR code upload failed: ' . $e->getMessage());
                return redirect()->back()
                    ->withErrors(['qr_code_image' => 'Failed to upload QR code image. Please try again.'])
                    ->withInput();
            }
        }

        $method = PaymentMethod::create($data);
        $this->logActivity('Created payment method', [
            'payment_method_id' => $method->id,
            'type' => $method->type,
            'label' => $method->label
        ]);
        return redirect()->route('admin.payment-methods.index')->with('success', 'Payment method added.');
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        return view('admin.payment_methods.edit', ['method' => $paymentMethod]);
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $data = $request->validate([
            'type' => 'required|in:gcash,paymaya',
            'label' => 'required|string|max:120',
            'account_name' => 'nullable|string|max:120',
            'account_number' => 'required|string|max:50',
            'is_active' => 'sometimes|boolean',
            'qr_code_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('qr_code_image')) {
            // Delete old image if exists
            if ($paymentMethod->qr_code_image) {
                try {
                    Storage::disk('payment_qr_codes')->delete($paymentMethod->qr_code_image);
                } catch (\Exception $e) {
                    \Log::warning('Failed to delete old QR code: ' . $e->getMessage());
                }
            }
            
            // Ensure target directory exists: public/storage/payment_qr_codes
            $target = public_path('storage/payment_qr_codes');
            if (!File::exists($target)) {
                File::makeDirectory($target, 0755, true);
            }
            
            try {
                $file = $request->file('qr_code_image');
                $filename = $file->hashName();
                $file->storeAs('', $filename, 'payment_qr_codes');
                $data['qr_code_image'] = $filename;
            } catch (\Exception $e) {
                \Log::error('QR code upload failed: ' . $e->getMessage());
                return redirect()->back()
                    ->withErrors(['qr_code_image' => 'Failed to upload QR code image. Please try again.'])
                    ->withInput();
            }
        }

        $paymentMethod->update($data);
        $this->logActivity('Updated payment method', [
            'payment_method_id' => $paymentMethod->id,
            'type' => $paymentMethod->type,
            'label' => $paymentMethod->label
        ]);
        return redirect()->route('admin.payment-methods.index')->with('success', 'Payment method updated.');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        if ($paymentMethod->qr_code_image) {
            try {
                Storage::disk('payment_qr_codes')->delete($paymentMethod->qr_code_image);
            } catch (\Exception $e) {
                \Log::warning('Failed to delete QR code file: ' . $e->getMessage());
            }
        }
        
        $id = $paymentMethod->id;
        $type = $paymentMethod->type;
        $label = $paymentMethod->label;
        
        try {
            $paymentMethod->delete();
            $this->logActivity('Deleted payment method', [
                'payment_method_id' => $id,
                'type' => $type,
                'label' => $label
            ]);
            return redirect()->route('admin.payment-methods.index')->with('success', 'Payment method deleted.');
        } catch (\Exception $e) {
            \Log::error('Failed to delete payment method: ' . $e->getMessage());
            return redirect()->route('admin.payment-methods.index')
                ->withErrors(['error' => 'Failed to delete payment method. Please try again.']);
        }
    }
}