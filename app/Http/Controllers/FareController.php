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
        // Checkbox: if present => true, if absent => false
        $validated['active'] = $request->has('active');

        $fare = Fare::create($validated);
        $this->logActivity('Created fare', "Fare created (ID {$fare->id})", [
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
        // Allow deactivation by unchecking the box
        $validated['active'] = $request->has('active');

        $fare->update($validated);
        $this->logActivity('Updated fare', "Fare updated (ID {$fare->id})", [
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
        $this->logActivity('Deleted fare', "Fare deleted (ID {$fareId})", [
            'fare_id' => $fareId
        ]);
        return redirect()->route('fares.index')->with('success', 'Fare deleted successfully!');
    }

    public function toggle(Fare $fare)
    {
        $fare->active = ! $fare->active;
        $fare->save();
        $action = $fare->active ? 'Activated fare' : 'Deactivated fare';
        $this->logActivity($action, "Fare status changed (ID {$fare->id})", [
            'fare_id' => $fare->id,
            'active' => $fare->active,
        ]);
        return back()->with('success', 'Fare '.($fare->active ? 'activated' : 'deactivated').' successfully!');
    }
}
