<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Fare;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function passenger(Request $request)
    {
        // Simplified validation - just check the essentials
        $request->validate([
            'origin' => 'required|string',
            'destination' => 'required|string', 
            'outbound_trip_id' => 'required|exists:trips,id',
        ]);

        $criteria = $request->only(['origin', 'destination', 'departure_date', 'return_date', 'tripType', 'adult', 'child', 'infant', 'pwd', 'student']);
        
        // Get the selected trips
        $outboundTrip = Trip::findOrFail($request->outbound_trip_id);
        $inboundTrip = $request->inbound_trip_id ? Trip::findOrFail($request->inbound_trip_id) : null;

        // Get fares for pricing calculation (with fallback)
        $fares = Fare::where('active', true)->pluck('price', 'passenger_type');
        if ($fares->isEmpty()) {
            $fares = collect(['adult' => 100, 'child' => 80, 'infant' => 0, 'pwd' => 80]);
        }

        return view('bookings.passenger', compact('criteria', 'outboundTrip', 'inboundTrip', 'fares'));
    }

    public function create(Trip $trip)
    {
        // Base fares by passenger type
        $fares = Fare::where('active', true)->pluck('price', 'passenger_type');
        return view('bookings.create', compact('trip', 'fares'));
    }

    public function summary(Request $request)
    {
        // Validate the passenger and contact data
        $validated = $request->validate([
            'trip_id' => 'required|exists:trips,id',
            'return_trip_id' => 'sometimes|nullable|exists:trips,id',
            'adult' => 'required|integer|min:0',
            'child' => 'required|integer|min:0',
            'infant' => 'required|integer|min:0',
            'pwd' => 'required|integer|min:0',
            'student' => 'required|integer|min:0',
            
            // Individual passenger data
            'passengers' => 'required|array|min:1',
            'passengers.*.first_name' => 'required|string|max:100',
            'passengers.*.last_name' => 'required|string|max:100',
            'passengers.*.gender' => 'required|in:male,female',
            'passengers.*.birth_date' => 'required|date',
            'passengers.*.type' => 'required|in:adult,child,infant,pwd,student',
            'passengers.*.fare' => 'required|numeric|min:0',
            
            // Optional fields for specific passenger types
            'passengers.*.student_id' => 'sometimes|nullable|string|max:50',
            'passengers.*.school' => 'sometimes|nullable|string|max:200',
            'passengers.*.id_number' => 'sometimes|nullable|string|max:50',
            
            // Contact information
            'contact_name' => 'required|string|max:120',
            'contact_email' => 'required|email|max:120',
            'contact_phone' => 'required|string|max:20',
            'contact_phone_alt' => 'sometimes|nullable|string|max:20',
            'contact_address' => 'sometimes|nullable|string|max:500',
            'special_requests' => 'sometimes|nullable|string|max:1000',
            'contact_preferences' => 'sometimes|nullable|array',
            'contact_preferences.*' => 'in:email,sms,call',
        ]);

        // Get trips
        $outboundTrip = Trip::findOrFail($validated['trip_id']);
        $inboundTrip = isset($validated['return_trip_id']) && !empty($validated['return_trip_id']) ? Trip::findOrFail($validated['return_trip_id']) : null;

        // Process passengers data
        $passengers = $validated['passengers'];
        $contactInfo = [
            'contact_name' => $validated['contact_name'],
            'contact_email' => $validated['contact_email'],
            'contact_phone' => $validated['contact_phone'],
            'contact_phone_alt' => $validated['contact_phone_alt'] ?? null,
            'contact_address' => $validated['contact_address'] ?? null,
            'special_requests' => $validated['special_requests'] ?? null,
            'contact_preferences' => $validated['contact_preferences'] ?? [],
        ];

        // Calculate totals from individual passenger fares
        $totalFare = 0;
        foreach ($passengers as $passenger) {
            $totalFare += floatval($passenger['fare']);
        }

        // Count passengers by type
        $counts = [
            'adult' => (int) $validated['adult'],
            'child' => (int) $validated['child'],
            'infant' => (int) $validated['infant'],
            'pwd' => (int) $validated['pwd'],
            'student' => (int) $validated['student'],
        ];

        // Store this step in session for checkout
        session([
            'booking.summary' => [
                'outbound_trip_id' => $outboundTrip->id,
                'inbound_trip_id' => $inboundTrip ? $inboundTrip->id : null,
                'passengers' => $passengers,
                'contact_info' => $contactInfo,
                'counts' => $counts,
                'total_fare' => $totalFare,
                'grand_total' => $totalFare * ($inboundTrip ? 2 : 1),
            ],
        ]);

        return view('bookings.summary', [
            'outboundTrip' => $outboundTrip,
            'inboundTrip' => $inboundTrip,
            'passengers' => $passengers,
            'contactInfo' => $contactInfo,
            'counts' => $counts,
            'totalFare' => $totalFare,
            'grandTotal' => $totalFare * ($inboundTrip ? 2 : 1),
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

        $validationRules = [
            'payment_method' => 'required|in:cod,card,gcash,paymaya',
            'terms' => 'required|accepted',
        ];

        // Add card validation if card payment is selected
        if ($request->payment_method === 'card') {
            $validationRules = array_merge($validationRules, [
                'card_number' => 'required|string|min:13|max:19',
                'card_expiry' => 'required|string|regex:/^(0[1-9]|1[0-2])\/([0-9]{2})$/',
                'card_cvv' => 'required|string|min:3|max:4',
                'card_name' => 'required|string|max:100',
            ]);
        }

        $request->validate($validationRules);

        // Get trip data - handle both old and new data structures
        $tripId = $data['trip_id'] ?? $data['outbound_trip_id'];
        $trip = Trip::findOrFail($tripId);

        $booking = \App\Models\Booking::create([
            'trip_id' => $trip->id,
            'origin' => $trip->origin,
            'destination' => $trip->destination,
            'departure_time' => $trip->departure_time,
            'adult' => $data['counts']['adult'],
            'child' => $data['counts']['child'],
            'infant' => $data['counts']['infant'],
            'pwd' => $data['counts']['pwd'],
            'full_name' => $data['contact_info']['contact_name'] ?? $data['customer']['full_name'] ?? 'Guest',
            'email' => $data['contact_info']['contact_email'] ?? $data['customer']['email'] ?? '',
            'phone' => $data['contact_info']['contact_phone'] ?? $data['customer']['phone'] ?? null,
            'total_amount' => $data['grand_total'] ?? $data['subtotal'] ?? 0,
            'payment_method' => $request->payment_method,
            'status' => 'pending',
        ]);

        // Here you’d integrate real payment. For now, mark confirmed for COD.
        // Process payment based on method
        $paymentStatus = $this->processPayment($request, $booking);

        if ($paymentStatus['success']) {
            $booking->update([
                'status' => $paymentStatus['status'],
            ]);
        } else {
            // Payment failed
            $booking->update(['status' => 'failed']);
            
            return back()->withErrors(['payment' => $paymentStatus['message']])
                ->withInput();
        }

        // Clear session step data
        session()->forget('booking.summary');

        return redirect()->route('bookings.confirmation', $booking)
            ->with('success', $paymentStatus['message'] ?? 'Booking processed successfully!');
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

    private function processPayment(Request $request, $booking)
    {
        $paymentMethod = $request->payment_method;

        switch ($paymentMethod) {
            case 'cod':
                return [
                    'success' => true,
                    'status' => 'confirmed',
                    'message' => 'Booking confirmed! Please pay at the terminal before departure.',
                ];

            case 'gcash':
                // In a real implementation, you'd integrate with GCash API
                return $this->simulateDigitalWalletPayment('GCash', $booking);

            case 'paymaya':
                // In a real implementation, you'd integrate with PayMaya API
                return $this->simulateDigitalWalletPayment('PayMaya', $booking);

            case 'card':
                // In a real implementation, you'd integrate with Stripe, PayPal, or local payment processor
                return $this->simulateCardPayment($request, $booking);

            default:
                return [
                    'success' => false,
                    'status' => 'failed',
                    'message' => 'Invalid payment method selected.'
                ];
        }
    }

    private function simulateDigitalWalletPayment($walletType, $booking)
    {
        // Simulate API call - in production, replace with actual API integration
        $success = rand(1, 10) > 1; // 90% success rate for simulation
        
        if ($success) {
            return [
                'success' => true,
                'status' => 'confirmed',
                'message' => "Payment successful via {$walletType}! Your booking is confirmed.",
            ];
        } else {
            return [
                'success' => false,
                'status' => 'failed',
                'message' => "{$walletType} payment failed. Please try again or use a different payment method."
            ];
        }
    }

    private function simulateCardPayment(Request $request, $booking)
    {
        // Simulate card processing - in production, integrate with actual payment processor
        $cardNumber = str_replace(' ', '', $request->card_number);
        
        // Basic validation
        if (strlen($cardNumber) < 13 || strlen($cardNumber) > 19) {
            return [
                'success' => false,
                'status' => 'failed',
                'message' => 'Invalid card number. Please check and try again.'
            ];
        }

        // Simulate processing
        $success = rand(1, 10) > 2; // 80% success rate for simulation
        
        if ($success) {
            return [
                'success' => true,
                'status' => 'confirmed',
                'message' => 'Card payment successful! Your booking is confirmed.',
            ];
        } else {
            return [
                'success' => false,
                'status' => 'failed',
                'message' => 'Card payment failed. Please check your card details and try again.'
            ];
        }
    }
}
