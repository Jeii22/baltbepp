<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $methods = PaymentMethod::orderBy('type')->get();
        return view('admin.payment_methods.index', compact('methods'));
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
        ]);
        $data['is_active'] = $request->boolean('is_active');

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
        ]);
        $data['is_active'] = $request->boolean('is_active');

        $paymentMethod->update($data);
        return redirect()->route('admin.payment-methods.index')->with('success', 'Payment method updated.');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        $paymentMethod->delete();
        return redirect()->route('admin.payment-methods.index')->with('success', 'Payment method deleted.');
    }
}