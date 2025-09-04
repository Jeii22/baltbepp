<?php

namespace App\Http\Controllers;

use App\Models\Fare;
use Illuminate\Http\Request;

class FareController extends Controller
{
    public function index()
    {
        $fares = Fare::orderBy('passenger_type')->get();
        return view('fares.index', compact('fares'));
    }

    public function create()
    {
        return view('fares.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'passenger_type' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'active' => 'nullable|boolean',
        ]);
        $validated['active'] = $request->boolean('active', true);

        Fare::create($validated);
        return redirect()->route('fares.index')->with('success', 'Fare created successfully!');
    }

    public function edit(Fare $fare)
    {
        return view('fares.edit', compact('fare'));
    }

    public function update(Request $request, Fare $fare)
    {
        $validated = $request->validate([
            'passenger_type' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'active' => 'nullable|boolean',
        ]);
        $validated['active'] = $request->boolean('active', true);

        $fare->update($validated);
        return redirect()->route('fares.index')->with('success', 'Fare updated successfully!');
    }

    public function destroy(Fare $fare)
    {
        $fare->delete();
        return redirect()->route('fares.index')->with('success', 'Fare deleted successfully!');
    }
}
