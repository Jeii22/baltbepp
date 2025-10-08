<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Cancelled</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #dc2626, #ef4444);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f8fafc;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .booking-info {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #ef4444;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            color: #374151;
        }
        .value {
            color: #1f2937;
        }
        .cancelled-badge {
            background: #ef4444;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            display: inline-block;
            margin: 10px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            background: #1f2937;
            color: white;
            border-radius: 8px;
        }
        .refund-info {
            background: #dbeafe;
            border: 1px solid #3b82f6;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .apology-section {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }
        .contact-info {
            background: #f0fdf4;
            border: 1px solid #22c55e;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>❌ Booking Cancelled</h1>
        <p>We apologize for the inconvenience</p>
    </div>

    <div class="content">
        <div class="cancelled-badge">🚫 Payment {{ ucfirst($booking->status) }}</div>
        
        <div class="apology-section">
            <h3>🙏 We Sincerely Apologize</h3>
            <p>We're truly sorry that we couldn't process your booking. We understand how disappointing this must be, especially when you were looking forward to your ferry journey.</p>
        </div>

        <p>Dear {{ $booking->full_name }},</p>
        
        <p>Unfortunately, your booking could not be completed due to: <strong>{{ $reason }}</strong></p>

        <div class="booking-info">
            <h3>📋 Cancelled Booking Details</h3>
            
            <div class="info-row">
                <span class="label">Booking Reference:</span>
                <span class="value">{{ $booking->payment_reference ?? 'BLT-' . str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
            
            <div class="info-row">
                <span class="label">Route:</span>
                <span class="value">{{ $booking->origin }} → {{ $booking->destination }}</span>
            </div>
            
            <div class="info-row">
                <span class="label">Departure:</span>
                <span class="value">{{ $booking->departure_time->format('F j, Y - g:i A') }}</span>
            </div>
            
            <div class="info-row">
                <span class="label">Passengers:</span>
                <span class="value">
                    @php
                        $passengerTypes = [];
                        if($booking->adult > 0) $passengerTypes[] = $booking->adult . ' Adult' . ($booking->adult > 1 ? 's' : '');
                        if($booking->child > 0) $passengerTypes[] = $booking->child . ' Child' . ($booking->child > 1 ? 'ren' : '');
                        if($booking->infant > 0) $passengerTypes[] = $booking->infant . ' Infant' . ($booking->infant > 1 ? 's' : '');
                        if($booking->pwd > 0) $passengerTypes[] = $booking->pwd . ' PWD/Senior' . ($booking->pwd > 1 ? 's' : '');
                        if($booking->student > 0) $passengerTypes[] = $booking->student . ' Student' . ($booking->student > 1 ? 's' : '');
                    @endphp
                    {{ implode(', ', $passengerTypes) }}
                </span>
            </div>
            
            <div class="info-row">
                <span class="label">Amount:</span>
                <span class="value">₱{{ number_format($booking->total_amount, 2) }}</span>
            </div>
            
            <div class="info-row">
                <span class="label">Payment Method:</span>
                <span class="value">{{ ucfirst(str_replace('_', ' ', $booking->payment_method)) }}</span>
            </div>
        </div>

        <div class="refund-info">
            <h4>💰 Refund Information</h4>
            <p><strong>Good news!</strong> If any payment was processed, we will initiate a full refund within 3-5 business days.</p>
            <ul>
                <li><strong>Refund Amount:</strong> ₱{{ number_format($booking->total_amount, 2) }}</li>
                <li><strong>Refund Method:</strong> Original payment method</li>
                <li><strong>Processing Time:</strong> 3-5 business days</li>
                <li><strong>Refund Reference:</strong> Will be provided via email with screenshot proof</li>
            </ul>
            <p><em>Note: Our customer service team will manually process your refund and send you a confirmation email with screenshot proof of the transaction.</em></p>
        </div>

        <div class="contact-info">
            <h4>📞 Need Immediate Assistance?</h4>
            <p>Our customer service team is here to help you:</p>
            <ul>
                <li><strong>Email:</strong> support@baltbep.com</li>
                <li><strong>Phone:</strong> +63 123 456 7890</li>
                <li><strong>Live Chat:</strong> Available on our website</li>
                <li><strong>Office Hours:</strong> Monday - Sunday, 6:00 AM - 8:00 PM</li>
            </ul>
            <p>Please reference your booking ID: <strong>{{ $booking->id }}</strong> when contacting us.</p>
        </div>

        <p>We would love the opportunity to serve you in the future. Please don't hesitate to book with us again, and we'll make sure your next experience is smooth and hassle-free.</p>
        
        <p>Once again, we sincerely apologize for any inconvenience caused.</p>
        
        <p>Warm regards,</p>
        <p><strong>The BaltBep Ferry Customer Service Team</strong></p>
    </div>

    <div class="footer">
        <h3>BaltBep Ferry Services</h3>
        <p>📧 support@baltbep.com | 📞 +63 123 456 7890</p>
        <p>🌐 www.baltbep.com</p>
        <p style="font-size: 12px; margin-top: 15px;">
            This is an automated email. Please do not reply to this message.<br>
            For support, please use the contact information provided above.
        </p>
    </div>
</body>
</html>