<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
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
            $data['qr_code_image'] = $request->file('qr_code_image')->store('payment_qr_codes', 'public');
        }

        PaymentMethod::create($data);
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
                \Storage::disk('public')->delete($paymentMethod->qr_code_image);
            }
            $data['qr_code_image'] = $request->file('qr_code_image')->store('payment_qr_codes', 'public');
        }

        $paymentMethod->update($data);
        return redirect()->route('admin.payment-methods.index')->with('success', 'Payment method updated.');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        if ($paymentMethod->qr_code_image) {
            \Storage::disk('public')->delete($paymentMethod->qr_code_image);
        }
        $paymentMethod->delete();
        return redirect()->route('admin.payment-methods.index')->with('success', 'Payment method deleted.');
    }
}