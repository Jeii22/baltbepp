<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Illuminate\Http\Request;

class TripController extends Controller
{
    public function index()
    {
        $trips = Trip::all();
        return view('trips.index', compact('trips'));
    }

    public function create()
    {
        return view('trips.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'origin' => 'required',
            'destination' => 'required',
            'departure_time' => 'required|date',
            'arrival_time' => 'required|date',
            'capacity' => 'required|integer',
            'price' => 'required|numeric',
        ]);

        Trip::create($request->all());
        return redirect()->route('trips.index')->with('success', 'Trip created successfully!');
    }

    public function edit(Trip $trip)
    {
        return view('trips.edit', compact('trip'));
    }

    public function update(Request $request, Trip $trip)
    {
        $trip->update($request->all());
        return redirect()->route('trips.index')->with('success', 'Trip updated successfully!');
    }

    public function destroy(Trip $trip)
    {
        $trip->delete();
        return redirect()->route('trips.index')->with('success', 'Trip deleted successfully!');
    }
}
