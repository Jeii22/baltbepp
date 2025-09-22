<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Paymongo\PaymongoClient; // PayMongo SDK client

class PaymentService
{
    /**
     * Process payment based on the selected method
     */
    public function processPayment(Request $request, Booking $booking): array
    {
        $paymentMethod = $request->payment_method;

        try {
            switch ($paymentMethod) {
                case 'cod':
                    return $this->processCashOnDeparture($booking);

                case 'gcash':
                    // Route standard GCash choice through PayMongo for hosted checkout (QR + Continue)
                    return $this->processPayMongoGCash($request, $booking);

                case 'paymaya':
                    return $this->processPayMayaPayment($request, $booking);

                case 'card':
                    return $this->processCardPayment($request, $booking);

                case 'paymongo_card':
                    if (!Setting::getBool('paymongo_enabled', true)) {
                        return [
                            'success' => false,
                            'status' => 'failed',
                            'message' => 'PayMongo is currently disabled by the administrator.'
                        ];
                    }
                    return $this->processPayMongoCard($request, $booking);

                case 'paymongo_gcash':
                    if (!Setting::getBool('paymongo_enabled', true)) {
                        return [
                            'success' => false,
                            'status' => 'failed',
                            'message' => 'PayMongo is currently disabled by the administrator.'
                        ];
                    }
                    return $this->processPayMongoGCash($request, $booking);

                default:
                    return [
                        'success' => false,
                        'status' => 'failed',
                        'message' => 'Invalid payment method selected.'
                    ];
            }
        } catch (Exception $e) {
            Log::error('Payment processing error: ' . $e->getMessage(), [
                'booking_id' => $booking->id,
                'payment_method' => $paymentMethod,
                'error' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'status' => 'failed',
                'message' => 'Payment processing failed. Please try again.'
            ];
        }
    }

    /**
     * Process Cash on Departure payment
     */
    private function processCashOnDeparture(Booking $booking): array
    {
        return [
            'success' => true,
            'status' => 'confirmed',
            'message' => 'Booking confirmed! Please pay at the terminal before departure.',
            'payment_reference' => 'COD-' . $booking->id . '-' . time()
        ];
    }

    /**
     * Process GCash payment (simulation)
     */
    private function processGCashPayment(Request $request, Booking $booking): array
    {
        // In production, integrate with GCash API
        // For now, simulate the payment process
        
        $phoneNumber = $request->gcash_phone ?? '';
        
        if (empty($phoneNumber)) {
            return [
                'success' => false,
                'status' => 'failed',
                'message' => 'GCash phone number is required.'
            ];
        }

        // Simulate API call with 90% success rate
        $success = rand(1, 10) > 1;
        
        if ($success) {
            $reference = 'GCASH-' . strtoupper(uniqid()) . '-' . time();
            
            return [
                'success' => true,
                'status' => 'confirmed',
                'message' => 'GCash payment successful! Your booking is confirmed.',
                'payment_reference' => $reference
            ];
        } else {
            return [
                'success' => false,
                'status' => 'failed',
                'message' => 'GCash payment failed. Please check your account balance and try again.'
            ];
        }
    }

    /**
     * Process PayMaya payment (simulation)
     */
    private function processPayMayaPayment(Request $request, Booking $booking): array
    {
        // In production, integrate with PayMaya API
        // For now, simulate the payment process
        
        $phoneNumber = $request->paymaya_phone ?? '';
        
        if (empty($phoneNumber)) {
            return [
                'success' => false,
                'status' => 'failed',
                'message' => 'PayMaya phone number is required.'
            ];
        }

        // Simulate API call with 85% success rate
        $success = rand(1, 10) > 1.5;
        
        if ($success) {
            $reference = 'MAYA-' . strtoupper(uniqid()) . '-' . time();
            
            return [
                'success' => true,
                'status' => 'confirmed',
                'message' => 'PayMaya payment successful! Your booking is confirmed.',
                'payment_reference' => $reference
            ];
        } else {
            return [
                'success' => false,
                'status' => 'failed',
                'message' => 'PayMaya payment failed. Please check your account and try again.'
            ];
        }
    }

    /**
     * Process Credit/Debit Card payment (simulation)
     */
    private function processCardPayment(Request $request, Booking $booking): array
    {
        // Validate card details
        $cardNumber = str_replace(' ', '', $request->card_number ?? '');
        $cardExpiry = $request->card_expiry ?? '';
        $cardCvv = $request->card_cvv ?? '';
        $cardName = $request->card_name ?? '';

        // Basic validation
        if (strlen($cardNumber) < 13 || strlen($cardNumber) > 19) {
            return [
                'success' => false,
                'status' => 'failed',
                'message' => 'Invalid card number. Please check and try again.'
            ];
        }

        if (empty($cardExpiry) || !preg_match('/^\d{2}\/\d{2}$/', $cardExpiry)) {
            return [
                'success' => false,
                'status' => 'failed',
                'message' => 'Invalid expiry date. Please use MM/YY format.'
            ];
        }

        if (empty($cardCvv) || strlen($cardCvv) < 3 || strlen($cardCvv) > 4) {
            return [
                'success' => false,
                'status' => 'failed',
                'message' => 'Invalid CVV. Please check and try again.'
            ];
        }

        if (empty($cardName)) {
            return [
                'success' => false,
                'status' => 'failed',
                'message' => 'Cardholder name is required.'
            ];
        }

        // Simulate card processing with 80% success rate
        $success = rand(1, 10) > 2;
        
        if ($success) {
            $reference = 'CARD-' . strtoupper(uniqid()) . '-' . time();
            
            return [
                'success' => true,
                'status' => 'confirmed',
                'message' => 'Card payment successful! Your booking is confirmed.',
                'payment_reference' => $reference
            ];
        } else {
            return [
                'success' => false,
                'status' => 'failed',
                'message' => 'Card payment failed. Please check your card details and try again.'
            ];
        }
    }

    /**
     * Process PayMongo Card payment (real integration)
     */
    private function processPayMongoCard(Request $request, Booking $booking): array
    {
        // This would integrate with PayMongo API for real card processing
        // For now, return simulation
        return $this->processCardPayment($request, $booking);
    }

    /**
     * Process PayMongo GCash payment (real integration)
     */
    private function processPayMongoGCash(Request $request, Booking $booking): array
    {
        // Create a real PayMongo GCash source and return redirect URL
        $amountInCentavos = (int) round(($booking->total_amount ?? 0) * 100);

        $successUrl = route('payments.paymongo.gcash.success', $booking);
        $failedUrl  = route('payments.paymongo.gcash.failed', $booking);

        $client = new PaymongoClient(env('PAYMONGO_SECRET_KEY'));
        $source = $client->sources->create([
            'type'     => 'gcash',
            'amount'   => $amountInCentavos,
            'currency' => 'PHP',
            'redirect' => [
                'success' => $successUrl,
                'failed'  => $failedUrl,
            ],
            'billing'  => [
                'name'  => $booking->full_name,
                'email' => $booking->email,
                'phone' => $booking->phone,
            ],
        ]);

        return [
            'success' => true,
            'status' => 'pending',
            'message' => 'Redirecting to GCash…',
            'payment_reference' => $source->id ?? null,
            'redirect_url' => $source->redirect['checkout_url'] ?? null,
        ];
    }

    /**
     * Validate payment data based on method
     */
    public function validatePaymentData(Request $request): array
    {
        $paymentMethod = $request->payment_method;
        $errors = [];

        switch ($paymentMethod) {
            case 'gcash':
            case 'paymongo_gcash':
                // No phone number required for hosted GCash checkout
                break;

            case 'paymaya':
                if (empty($request->paymaya_phone)) {
                    $errors['paymaya_phone'] = 'PayMaya phone number is required.';
                } elseif (!preg_match('/^(09|\+639)\d{9}$/', $request->paymaya_phone)) {
                    $errors['paymaya_phone'] = 'Please enter a valid Philippine mobile number.';
                }
                break;

            case 'card':
            case 'paymongo_card':
                if (empty($request->card_number)) {
                    $errors['card_number'] = 'Card number is required.';
                }
                if (empty($request->card_expiry)) {
                    $errors['card_expiry'] = 'Expiry date is required.';
                }
                if (empty($request->card_cvv)) {
                    $errors['card_cvv'] = 'CVV is required.';
                }
                if (empty($request->card_name)) {
                    $errors['card_name'] = 'Cardholder name is required.';
                }
                break;
        }

        return $errors;
    }

    /**
     * Generate payment reference number
     */
    public function generatePaymentReference(string $method, int $bookingId): string
    {
        $prefix = strtoupper(substr($method, 0, 4));
        return $prefix . '-' . $bookingId . '-' . time() . '-' . rand(1000, 9999);
    }
}