<?php

namespace App\Http\Controllers;

use App\Models\Fare;
use Illuminate\Http\Request;

use App\Traits\LogsAdminActivity;

class FareController extends Controller
{
    use LogsAdminActivity;
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

        $fare = Fare::create($validated);
        $this->logActivity('Created fare', [
            'fare_id' => $fare->id,
            'passenger_type' => $fare->passenger_type,
            'price' => $fare->price
        ]);
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
        $this->logActivity('Updated fare', [
            'fare_id' => $fare->id,
            'passenger_type' => $fare->passenger_type,
            'price' => $fare->price
        ]);
        return redirect()->route('fares.index')->with('success', 'Fare updated successfully!');
    }

    public function destroy(Fare $fare)
    {
        $fareId = $fare->id;
        $fare->delete();
        $this->logActivity('Deleted fare', [
            'fare_id' => $fareId
        ]);
        return redirect()->route('fares.index')->with('success', 'Fare deleted successfully!');
    }
}
