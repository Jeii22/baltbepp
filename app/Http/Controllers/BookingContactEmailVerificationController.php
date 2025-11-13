<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Mail\ContactEmailVerification;

class BookingContactEmailVerificationController extends Controller
{
    /**
     * Send a verification code to the provided contact email.
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $email = strtolower($validated['email']);

        // Rate limit basic: allow only one send every 60 seconds per email in session
        $lastSentAt = session("booking_contact_email_last_sent_at_$email");
        if ($lastSentAt && now()->diffInSeconds($lastSentAt) < 60) {
            return response()->json([
                'ok' => false,
                'message' => 'Please wait a moment before requesting a new code.',
                'retry_after' => 60 - now()->diffInSeconds($lastSentAt),
            ], 429);
        }

        // Generate 6-digit numeric code
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Persist in session (scoped to booking flow)
        session([
            'booking_contact_email' => $email,
            'booking_contact_email_code' => $code,
            'booking_contact_email_expires_at' => now()->addMinutes(10),
            'booking_contact_email_verified' => false,
            "booking_contact_email_last_sent_at_$email" => now(),
            'booking_contact_email_attempts' => 0,
        ]);

        try {
            Mail::to($email)->send(new ContactEmailVerification($code));
        } catch (\Throwable $e) {
            Log::error('Contact email verification send failed', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'ok' => false,
                'message' => 'Failed to send verification email. Please try again later.',
            ], 500);
        }

        return response()->json([
            'ok' => true,
            'message' => 'Verification code sent successfully.',
            'expires_in' => 600,
        ]);
    }

    /**
     * Verify the provided code for the contact email.
     */
    public function verify(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ]);

        $email = strtolower($validated['email']);
        $code = $validated['code'];

        // Ensure email matches what was sent
        if (session('booking_contact_email') !== $email) {
            return response()->json([
                'ok' => false,
                'message' => 'Email mismatch. Request a new code for the current email.',
            ], 400);
        }

        $storedCode = session('booking_contact_email_code');
        $expiresAt = session('booking_contact_email_expires_at');
        $attempts = session('booking_contact_email_attempts', 0);

        if (!$storedCode || !$expiresAt) {
            return response()->json([
                'ok' => false,
                'message' => 'No active verification code. Please request a new one.',
            ], 404);
        }

        if (now()->greaterThan($expiresAt)) {
            return response()->json([
                'ok' => false,
                'message' => 'Code expired. Please request a new one.',
            ], 410);
        }

        if ($attempts >= 5) {
            return response()->json([
                'ok' => false,
                'message' => 'Too many attempts. Please request a new code.',
            ], 429);
        }

        // Increment attempts before comparison
        session(['booking_contact_email_attempts' => $attempts + 1]);

        if (!hash_equals($storedCode, $code)) {
            return response()->json([
                'ok' => false,
                'message' => 'Invalid code. Please try again.',
                'attempts_left' => max(0, 5 - ($attempts + 1)),
            ], 422);
        }

        // Mark verified
        session(['booking_contact_email_verified' => true]);

        return response()->json([
            'ok' => true,
            'message' => 'Email verified successfully.',
        ]);
    }
}
