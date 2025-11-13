<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Fare;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\Request;

class TripController extends Controller
{
    use LogsAdminActivity;
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
            'adult' => 'nullable|integer|min:0',
            'child' => 'nullable|integer|min:0',
            'infant' => 'nullable|integer|min:0',
            'pwd' => 'nullable|integer|min:0',
            'student' => 'nullable|integer|min:0',
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

        // Load active fares and map passenger types
        $fareRows = Fare::where('active', true)->get();
        $fareMap = [];
        
        // Create a direct mapping from database passenger types to their prices
        foreach ($fareRows as $fare) {
            $fareMap[$fare->passenger_type] = (float) $fare->price;
        }
        
        // Also create aliases for easier access in templates
        $fareAliases = [
            'adult' => $fareMap['Regular'] ?? 900,
            'child' => $fareMap['Child (2-11)'] ?? 450,
            'infant' => $fareMap['Infant'] ?? 0,
            'pwd' => $fareMap['Senior Citizen / PWD'] ?? 720,
            'student' => $fareMap['Student'] ?? 765,
        ];

        return view('bookings.search', [
            'criteria' => $validated,
            'outbound' => $outbound,
            'inbound' => $inbound,
            'fares' => $fareMap,
            'fareAliases' => $fareAliases,
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

        $trip = Trip::create($request->all());

        $this->logActivity('trip_created', "Created trip from {$trip->origin} to {$trip->destination} departing at {$trip->departure_time}", [
            'trip_id' => $trip->id,
            'origin' => $trip->origin,
            'destination' => $trip->destination,
            'departure_time' => $trip->departure_time,
            'capacity' => $trip->capacity,
        ]);

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

        $oldData = $trip->only(['origin', 'destination', 'departure_time', 'capacity']);
        $trip->update($validated);

        $this->logActivity('trip_updated', "Updated trip #{$trip->id} from {$trip->origin} to {$trip->destination}", [
            'trip_id' => $trip->id,
            'old_data' => $oldData,
            'new_data' => $validated,
        ]);

        return redirect()->route('trips.index')->with('success', 'Trip updated successfully!');
    }

    public function destroy(Trip $trip)
    {
        $tripInfo = [
            'trip_id' => $trip->id,
            'origin' => $trip->origin,
            'destination' => $trip->destination,
            'departure_time' => $trip->departure_time,
        ];

        $trip->delete();

        $this->logActivity('trip_deleted', "Deleted trip from {$tripInfo['origin']} to {$tripInfo['destination']} departing at {$tripInfo['departure_time']}", $tripInfo);

        return redirect()->route('trips.index')->with('success', 'Trip deleted successfully!');
    }
}
