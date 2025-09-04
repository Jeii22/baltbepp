<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Fare;
use Illuminate\Http\Request;

class TripController extends Controller
{
    public function index()
    {
        $now = now();

        $upcomingTrips = Trip::where('departure_time', '>=', $now)
            ->orderBy('departure_time', 'asc')
            ->get();

        // Show only the 10 most recent past departures by default
        $pastTrips = Trip::where('departure_time', '<', $now)
            ->orderBy('departure_time', 'desc')
            ->limit(10)
            ->get();

        return view('trips.index', compact('upcomingTrips', 'pastTrips'));
    }

    public function search(Request $request)
    {
        $validated = $request->validate([
            'origin' => 'required|in:Bantayan,Cadiz',
            'destination' => 'required|in:Bantayan,Cadiz|different:origin',
            'departure_date' => 'required|date',
            'return_date' => 'nullable|date|after_or_equal:departure_date',
            'tripType' => 'required|in:round,oneway',
            'adult' => 'nullable|integer|min:1',
            'child' => 'nullable|integer|min:0',
            'infant' => 'nullable|integer|min:0',
            'pwd' => 'nullable|integer|min:0',
        ]);

        $departureStart = \Carbon\Carbon::parse($validated['departure_date'])->startOfDay();
        $departureEnd = \Carbon\Carbon::parse($validated['departure_date'])->endOfDay();

        $outbound = Trip::where('origin', $validated['origin'])
            ->where('destination', $validated['destination'])
            ->whereBetween('departure_time', [$departureStart, $departureEnd])
            ->orderBy('departure_time')
            ->get();

        $inbound = collect();
        if (($validated['tripType'] ?? null) === 'round' && !empty($validated['return_date'])) {
            $returnStart = \Carbon\Carbon::parse($validated['return_date'])->startOfDay();
            $returnEnd = \Carbon\Carbon::parse($validated['return_date'])->endOfDay();
            $inbound = Trip::where('origin', $validated['destination'])
                ->where('destination', $validated['origin'])
                ->whereBetween('departure_time', [$returnStart, $returnEnd])
                ->orderBy('departure_time')
                ->get();
        }

        // Load active fares and map by type
        $fares = Fare::where('active', true)->get()->keyBy(fn($f) => strtolower($f->passenger_type));
        $fareMap = [
            'adult' => (float)($fares['adult']->price ?? 0),
            'child' => (float)($fares['child']->price ?? 0),
            'infant' => (float)($fares['infant']->price ?? 0),
            'pwd'   => (float)($fares['pwd']->price ?? 0),
        ];

        return view('bookings.search', [
            'criteria' => $validated,
            'outbound' => $outbound,
            'inbound' => $inbound,
            'fares'    => $fareMap,
        ]);
    }

    // Returns available departure dates for a given route and month
    public function availableDates(Request $request)
    {
        $request->validate([
            'origin' => 'required|in:Bantayan,Cadiz',
            'destination' => 'required|in:Bantayan,Cadiz|different:origin',
            'month' => 'nullable|date_format:Y-m', // e.g., 2025-09
        ]);

        $origin = $request->string('origin');
        $destination = $request->string('destination');
        $month = $request->input('month', now()->format('Y-m'));
        $start = \Carbon\Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end = (clone $start)->endOfMonth();

        $dates = Trip::where('origin', $origin)
            ->where('destination', $destination)
            ->whereBetween('departure_time', [$start, $end])
            ->orderBy('departure_time')
            ->get()
            ->groupBy(fn($t) => \Carbon\Carbon::parse($t->departure_time)->toDateString())
            ->keys()
            ->values();

        return response()->json([
            'month' => $start->format('Y-m'),
            'availableDates' => $dates,
        ]);
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
        $validated = $request->validate([
            'origin' => 'required',
            'destination' => 'required',
            'departure_time' => 'required|date',
            'arrival_time' => 'required|date',
            'capacity' => 'required|integer',
            'price' => 'required|numeric',
        ]);

        $trip->update($validated);
        return redirect()->route('trips.index')->with('success', 'Trip updated successfully!');
    }

    public function destroy(Trip $trip)
    {
        $trip->delete();
        return redirect()->route('trips.index')->with('success', 'Trip deleted successfully!');
    }
}
