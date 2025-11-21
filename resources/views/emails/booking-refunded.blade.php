<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Refund Successfully Processed</title>
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
            background: linear-gradient(135deg, #22c55e, #16a34a);
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
            border-left: 4px solid #22c55e;
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
        .success-badge {
            background: #22c55e;
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
            background: #d1fae5;
            border: 2px solid #22c55e;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .success-section {
            background: #d1fae5;
            border: 1px solid #22c55e;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }
        .contact-info {
            background: #dbeafe;
            border: 1px solid #3b82f6;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .amount-highlight {
            font-size: 24px;
            color: #22c55e;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>✅ Refund Successfully Processed</h1>
        <p>Your payment has been refunded</p>
    </div>

    <div class="content">
        <div class="success-badge">💚 Refund Completed</div>
        
        <div class="success-section">
            <h3>🎉 Good News!</h3>
            <p>Your refund has been successfully processed. The amount will be credited to your original payment method.</p>
        </div>

        <p>Dear {{ $booking->full_name }},</p>
        
        <p>We're writing to confirm that your refund has been successfully processed for your cancelled booking.</p>

        <div class="refund-info">
            <h3 style="margin-top: 0;">💰 Refund Details</h3>
            
            <div class="info-row">
                <span class="label">Refund Amount:</span>
                <span class="amount-highlight">₱{{ number_format($booking->total_amount, 2) }}</span>
            </div>
            
            <div class="info-row">
                <span class="label">Refund Status:</span>
                <span class="value" style="color: #22c55e; font-weight: bold;">✓ Successfully Processed</span>
            </div>
            
            <div class="info-row">
                <span class="label">Refund Method:</span>
                <span class="value">{{ ucfirst(str_replace('_', ' ', $booking->payment_method)) }}</span>
            </div>
            
            <div class="info-row">
                <span class="label">Processing Date:</span>
                <span class="value">{{ now()->format('F j, Y - g:i A') }}</span>
            </div>
            
            <div class="info-row">
                <span class="label">Expected Credit:</span>
                <span class="value">3-5 business days</span>
            </div>
        </div>

        <div class="booking-info">
            <h3>📋 Original Booking Details</h3>
            
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
        </div>

        <div class="contact-info">
            <h4>📞 Questions About Your Refund?</h4>
            <p>If you have any questions or concerns about your refund, our customer service team is here to help:</p>
            <ul>
                <li><strong>Email:</strong> support@baltbep.com</li>
                <li><strong>Phone:</strong> +63 123 456 7890</li>
                <li><strong>Live Chat:</strong> Available on our website</li>
                <li><strong>Office Hours:</strong> Monday - Sunday, 6:00 AM - 8:00 PM</li>
            </ul>
            <p>Please reference your booking ID: <strong>{{ $booking->id }}</strong> when contacting us.</p>
        </div>

        <p><strong>Important:</strong> Please allow 3-5 business days for the refund to appear in your account. The exact timing depends on your payment provider.</p>

        <p>We're sorry we couldn't serve you on this occasion, but we hope to welcome you aboard in the future. Thank you for choosing BaltBep Ferry Services.</p>

        <p>Safe travels!<br>
        <strong>The BaltBep Team</strong></p>
    </div>

    <div class="footer">
        <p>BaltBep Ferry Services</p>
        <p style="font-size: 12px; margin: 5px 0;">This is an automated refund confirmation email.</p>
        <p style="font-size: 12px; margin: 5px 0;">© {{ date('Y') }} BaltBep. All rights reserved.</p>
    </div>
</body>
</html>
