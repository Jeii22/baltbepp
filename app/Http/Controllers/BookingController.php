<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Fare;
use Illuminate\Http\Request;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Mail\BookingConfirmedMail;
use App\Mail\BookingRejectedMail;
use App\Services\TicketService;

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

        // Determine trips from session data
        $outboundTrip = Trip::findOrFail($data['outbound_trip_id'] ?? $data['trip_id']);
        $inboundTrip = isset($data['inbound_trip_id']) && !empty($data['inbound_trip_id'])
            ? Trip::find($data['inbound_trip_id'])
            : null;

        // Fares for display
        $fares = Fare::where('active', true)->pluck('price', 'passenger_type');

        // Load active admin-configured wallets for customer selection (guard if table missing)
        $wallets = collect();
        if (Schema::hasTable('payment_methods')) {
            $wallets = \App\Models\PaymentMethod::where('is_active', true)->orderBy('type')->get();
        }

        // Feature flags
        $paymongoEnabled = \App\Models\Setting::getBool('paymongo_enabled', true);

        return view('bookings.checkout', array_merge([
            'outboundTrip' => $outboundTrip,
            'inboundTrip' => $inboundTrip,
            'fares' => $fares,
            'grandTotal' => $data['grand_total'] ?? $data['grandTotal'] ?? 0,
            'wallets' => $wallets,
            'paymongoEnabled' => $paymongoEnabled,
        ], $data));
    }

    public function process(Request $request, PaymentService $paymentService)
    {
        $data = session('booking.summary');
        abort_if(!$data, 404);

        // Get available wallets
        $wallets = collect();
        if (Schema::hasTable('payment_methods')) {
            $wallets = \App\Models\PaymentMethod::where('is_active', true)->get();
        }

        $allowedWalletTypes = $wallets->pluck('type')->unique()->filter()->all();
        $allowedMethods = array_merge(['cod', 'card', 'paymongo_gcash'], $allowedWalletTypes);

        $validationRules = [
            'payment_method' => ['required', Rule::in($allowedMethods)],
            'terms' => 'required|accepted',
        ];

        $request->validate($validationRules);

        // Validate payment-specific data using PaymentService
        $paymentErrors = $paymentService->validatePaymentData($request);
        if (!empty($paymentErrors)) {
            return redirect()->back()->withErrors($paymentErrors)->withInput();
        }

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
            'student' => $data['counts']['student'] ?? 0,
            'full_name' => $data['contact_info']['contact_name'] ?? $data['customer']['full_name'] ?? 'Guest',
            'email' => $data['contact_info']['contact_email'] ?? $data['customer']['email'] ?? '',
            'phone' => $data['contact_info']['contact_phone'] ?? $data['customer']['phone'] ?? null,
            'total_amount' => $data['grand_total'] ?? $data['subtotal'] ?? 0,
            'payment_method' => $request->payment_method,
            'status' => 'pending',
        ]);

        // This method now only handles PayMongo/Card/COD payments
        // Digital wallets are handled by processDigitalWallet method
        if (in_array($request->payment_method, $allowedWalletTypes)) {
            return back()->withErrors(['payment' => 'Digital wallet payments should use the digital wallet flow.'])->withInput();
        }

        // Automated flows (PayMongo/Card/COD)
        $paymentStatus = $paymentService->processPayment($request, $booking);

        // If PayMongo returns a redirect URL (e.g., hosted checkout), show redirect page
        if (!empty($paymentStatus['redirect_url'])) {
            $booking->update([
                'status' => 'pending',
                'payment_reference' => $paymentStatus['payment_reference'] ?? null
            ]);

            // Keep session so user can come back if needed
            return view('payments.gcash', [
                'booking' => $booking,
                'checkoutUrl' => $paymentStatus['redirect_url'],
            ]);
        }

        if ($paymentStatus['success']) {
            // Normalize status to allowed enum values
            $normalizedStatus = in_array($paymentStatus['status'] ?? 'pending', ['pending','confirmed','cancelled'])
                ? $paymentStatus['status']
                : (($paymentStatus['status'] ?? '') === 'failed' ? 'cancelled' : 'pending');

            $booking->update([
                'status' => $normalizedStatus,
                'payment_reference' => $paymentStatus['payment_reference'] ?? null
            ]);
        } else {
            // Payment failed -> use allowed enum value
            $booking->update(['status' => 'cancelled']);
            
            return back()->withErrors(['payment' => $paymentStatus['message']])
                ->withInput();
        }

        // Clear session step data
        session()->forget('booking.summary');

        return redirect()->route('bookings.confirmation', $booking)
            ->with('success', $paymentStatus['message'] ?? 'Booking processed successfully!');
    }

    public function processDigitalWallet(Request $request, PaymentService $paymentService)
    {
        $data = session('booking.summary');
        abort_if(!$data, 404);

        // Get available wallets
        $wallets = collect();
        if (Schema::hasTable('payment_methods')) {
            $wallets = \App\Models\PaymentMethod::where('is_active', true)->get();
        }

        $allowedWalletTypes = $wallets->pluck('type')->unique()->filter()->all();

        $validationRules = [
            'payment_method' => ['required', Rule::in($allowedWalletTypes)],
            'terms' => 'required|accepted',
        ];

        // Add PayMaya phone validation if needed
        if ($request->payment_method === 'paymaya') {
            $validationRules['paymaya_phone'] = 'required|regex:/^09\d{9}$/';
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
            'student' => $data['counts']['student'] ?? 0,
            'full_name' => $data['contact_info']['contact_name'] ?? $data['customer']['full_name'] ?? 'Guest',
            'email' => $data['contact_info']['contact_email'] ?? $data['customer']['email'] ?? '',
            'phone' => $data['contact_info']['contact_phone'] ?? $data['customer']['phone'] ?? null,
            'total_amount' => $data['grand_total'] ?? $data['subtotal'] ?? 0,
            'payment_method' => $request->payment_method,
            'status' => 'pending',
            'payment_reference' => $paymentService->generatePaymentReference($request->payment_method, time()),
        ]);

        // Get the selected wallet details
        $selectedWallet = $wallets->where('type', $request->payment_method)->first();

        // Clear session step data
        session()->forget('booking.summary');

        // Show digital wallet payment page with QR code and instructions
        return view('bookings.digital-wallet-payment', [
            'booking' => $booking,
            'wallet' => $selectedWallet,
            'paymentMethod' => $request->payment_method,
            'paymentReference' => $booking->payment_reference,
        ]);
    }

    public function confirmation(\App\Models\Booking $booking)
    {
        return view('bookings.confirmation', compact('booking'));
    }

    public function status(\App\Models\Booking $booking)
    {
        // Ensure user can only check their own booking
        if (auth()->check() && $booking->user_id != auth()->id()) {
            abort(403);
        }

        return response()->json(['status' => $booking->status]);
    }

    // PayMongo success callback (user redirected here after approving)
    public function paymongoSuccess(\App\Models\Booking $booking)
    {
        // Show confirmation; final status should be set via webhook
        return redirect()->route('bookings.confirmation', $booking)
            ->with('success', 'Payment processing… We will confirm once the provider notifies us.');
    }

    // PayMongo failed/cancelled callback
    public function paymongoFailed(\App\Models\Booking $booking)
    {
        // Map to allowed enum value
        $booking->update(['status' => 'cancelled']);
        return redirect()->back()->withErrors(['payment' => 'Payment cancelled or failed.']);
    }

    // PayMongo webhook to finalize payment status
    public function paymongoWebhook(Request $request)
    {
        // Verify PayMongo signature header
        $payload = $request->getContent();
        $signatureHeader = $request->header('Paymongo-Signature');
        $webhookSecret = env('PAYMONGO_WEBHOOK_SECRET');

        try {
            $client = new \Paymongo\PaymongoClient(env('PAYMONGO_SECRET_KEY'));
            $event = $client->webhooks->constructEvent([
                'payload' => $payload,
                'signature_header' => $signatureHeader,
                'webhook_secret_key' => $webhookSecret,
            ]);
        } catch (\Throwable $e) {
            // Invalid signature or parsing error
            return response('invalid signature', 400);
        }

        $type = $event->type ?? '';
        $resource = $event->resource ?? [];

        // For payment.paid, locate booking by source id and mark confirmed
        if (str_contains($type, 'payment.paid')) {
            $sourceId = $resource['data']['attributes']['source']['id'] ?? null;
            $paymentId = $resource['data']['id'] ?? null;

            if ($sourceId) {
                $booking = \App\Models\Booking::where('payment_reference', $sourceId)->first();
                if ($booking) {
                    $booking->update([
                        'status' => 'confirmed',
                        'payment_reference' => $paymentId ?? $sourceId,
                    ]);
                }
            }
        }

        return response()->noContent();
    }

    // Superadmin: list + filter by origin & destination
    public function index(Request $request)
    {
        $query = \App\Models\Booking::query()->with('trip');

        // Basic filters
        if ($request->filled('origin')) {
            $query->where('origin', $request->string('origin'));
        }
        if ($request->filled('destination')) {
            $query->where('destination', $request->string('destination'));
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->string('payment_method'));
        }

        // Enhanced filters to match new UI
        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('id', $q)
                    ->orWhere('full_name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->date('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->date('end_date'));
        }

        // Booking status filter
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $bookings = $query->latest()->paginate(15)->withQueryString();

        // Get filter options
        $origins = \App\Models\Booking::distinct()->pluck('origin')->filter()->sort()->values();
        $destinations = \App\Models\Booking::distinct()->pluck('destination')->filter()->sort()->values();
        $paymentMethods = \App\Models\Booking::distinct()->pluck('payment_method')->filter()->sort()->values();

        return view('bookings.index', compact('bookings', 'origins', 'destinations', 'paymentMethods'));
    }

    // Superadmin: edit booking (minimal for now)
    public function edit(\App\Models\Booking $booking)
    {
        return view('bookings.edit', compact('booking'));
    }

    // Superadmin: update booking (allow editing contact and basic counts)
    public function update(Request $request, \App\Models\Booking $booking)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:120',
            'email' => 'required|email|max:120',
            'phone' => 'nullable|string|max:20',
            'adult' => 'required|integer|min:0',
            'child' => 'required|integer|min:0',
            'infant' => 'required|integer|min:0',
            'pwd' => 'required|integer|min:0',
            'student' => 'nullable|integer|min:0',
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);

        $booking->update($validated);

        return redirect()->route('bookings.index')->with('success', 'Booking updated successfully.');
    }

    // Superadmin: update status
    public function updateStatus(Request $request, \App\Models\Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled',
            'rejection_reason' => 'nullable|string|max:500', // For cancelled bookings
        ]);

        $oldStatus = $booking->status;
        $newStatus = $validated['status'];
        
        $booking->update(['status' => $newStatus]);

        // Send email notifications based on status change
        $this->handleStatusChangeNotification($booking, $oldStatus, $newStatus, $validated['rejection_reason'] ?? null);

        return redirect()->back()->with('success', 'Booking status updated and notification sent.');
    }

    /**
     * Handle email notifications when booking status changes
     */
    private function handleStatusChangeNotification(\App\Models\Booking $booking, $oldStatus, $newStatus, $rejectionReason = null)
    {
        // Only send notifications if status actually changed and booking has email
        if ($oldStatus === $newStatus || empty($booking->email)) {
            return;
        }

        try {
            if ($newStatus === 'confirmed') {
                $this->sendBookingConfirmedEmail($booking);
            } elseif ($newStatus === 'cancelled') {
                $this->sendBookingRejectedEmail($booking, $rejectionReason);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send booking notification email for booking ' . $booking->id . ': ' . $e->getMessage());
            // Don't throw the error to avoid breaking the status update
        }
    }

    /**
     * Send booking confirmed email with ticket attachment
     */
    private function sendBookingConfirmedEmail(\App\Models\Booking $booking)
    {
        $ticketService = new TicketService();
        $ticketPath = $ticketService->generateTicketForEmail($booking);
        
        Mail::to($booking->email)->send(new BookingConfirmedMail($booking, $ticketPath));
        
        // Clean up the ticket file after sending (optional)
        if ($ticketPath && file_exists($ticketPath)) {
            // Keep the file for now - you might want to keep tickets for records
            // unlink($ticketPath);
        }
    }

    /**
     * Send booking rejected/cancelled email
     */
    private function sendBookingRejectedEmail(\App\Models\Booking $booking, $reason = null)
    {
        Mail::to($booking->email)->send(new BookingRejectedMail($booking, $reason));
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
