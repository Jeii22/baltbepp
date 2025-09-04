<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Fare;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function create(Trip $trip)
    {
        // Base fares by passenger type
        $fares = Fare::where('active', true)->pluck('price', 'passenger_type');
        return view('bookings.create', compact('trip', 'fares'));
    }

    public function summary(Request $request)
    {
        $validated = $request->validate([
            'trip_id' => 'required|exists:trips,id',
            'adult' => 'required|integer|min:1',
            'child' => 'required|integer|min:0',
            'infant' => 'required|integer|min:0',
            'pwd' => 'required|integer|min:0',
            'full_name' => 'required|string|max:120',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:50',
        ]);

        $trip = Trip::findOrFail($validated['trip_id']);

        // Read fares and normalize keys to expected types (adult, child, infant, pwd)
        $fareRows = Fare::where('active', true)->get();
        $fareMap = [ 'adult' => 0.0, 'child' => 0.0, 'infant' => 0.0, 'pwd' => 0.0 ];
        foreach ($fareRows as $f) {
            $name = strtolower($f->passenger_type);
            $price = (float) $f->price;
            if (str_contains($name, 'adult') || str_contains($name, 'regular')) {
                $fareMap['adult'] = $price;
            } elseif (str_contains($name, 'child')) {
                $fareMap['child'] = $price;
            } elseif (str_contains($name, 'infant') || str_contains($name, 'baby')) {
                $fareMap['infant'] = $price;
            } elseif (str_contains($name, 'pwd') || str_contains($name, 'senior')) {
                $fareMap['pwd'] = $price;
            }
        }

        $counts = [
            'adult' => (int) $validated['adult'],
            'child' => (int) $validated['child'],
            'infant' => (int) $validated['infant'],
            'pwd' => (int) $validated['pwd'],
        ];
        $allPax = $counts['adult'] + $counts['child'] + $counts['infant'] + $counts['pwd'];

        // Pricing model 3: base trip price × all passengers + per-type fares × counts
        $baseTotal = (float) $trip->price * $allPax;
        $fareTotal = ($counts['adult'] * $fareMap['adult'])
                   + ($counts['child'] * $fareMap['child'])
                   + ($counts['infant'] * $fareMap['infant'])
                   + ($counts['pwd']   * $fareMap['pwd']);
        $subtotal = $baseTotal + $fareTotal;

        // Store this step in session for checkout
        session([
            'booking.summary' => [
                'trip_id' => $trip->id,
                'counts' => $counts,
                'customer' => [
                    'full_name' => $validated['full_name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'] ?? '',
                ],
                'fares' => $fareMap,
                'subtotal' => $subtotal,
            ],
        ]);

        return view('bookings.summary', [
            'trip' => $trip,
            'counts' => $counts,
            'fares' => $fareMap,
            'customer' => [
                'full_name' => $validated['full_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? '',
            ],
            'subtotal' => $subtotal,
        ]);
    }

    public function checkout(Request $request)
    {
        $data = session('booking.summary');
        abort_if(!$data, 404);

        $trip = Trip::findOrFail($data['trip_id']);
        return view('bookings.checkout', compact('trip') + $data);
    }

    public function process(Request $request)
    {
        $data = session('booking.summary');
        abort_if(!$data, 404);

        $request->validate([
            'payment_method' => 'required|in:cod,card',
        ]);

        $trip = Trip::findOrFail($data['trip_id']);

        $booking = \App\Models\Booking::create([
            'trip_id' => $trip->id,
            'origin' => $trip->origin,
            'destination' => $trip->destination,
            'departure_time' => $trip->departure_time,
            'adult' => $data['counts']['adult'],
            'child' => $data['counts']['child'],
            'infant' => $data['counts']['infant'],
            'pwd' => $data['counts']['pwd'],
            'full_name' => $data['customer']['full_name'],
            'email' => $data['customer']['email'],
            'phone' => $data['customer']['phone'] ?? null,
            'total_amount' => $data['subtotal'],
            'status' => 'pending',
        ]);

        // Here you’d integrate real payment. For now, mark confirmed for COD.
        if ($request->payment_method === 'cod') {
            $booking->update(['status' => 'confirmed']);
        }

        // Clear session step data
        session()->forget('booking.summary');

        return redirect()->route('bookings.confirmation', $booking);
    }

    public function confirmation(\App\Models\Booking $booking)
    {
        return view('bookings.confirmation', compact('booking'));
    }

    // Superadmin: list + filter by origin & destination
    public function index(Request $request)
    {
        $query = \App\Models\Booking::query()->with('trip');

        if ($request->filled('origin')) {
            $query->where('origin', $request->string('origin'));
        }
        if ($request->filled('destination')) {
            $query->where('destination', $request->string('destination'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $bookings = $query->latest()->paginate(15)->withQueryString();

        return view('bookings.index', compact('bookings'));
    }

    // Superadmin: update status
    public function updateStatus(Request $request, \App\Models\Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);

        $booking->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', 'Booking status updated.');
    }
}
