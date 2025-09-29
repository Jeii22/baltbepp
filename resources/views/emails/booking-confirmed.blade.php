<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed</title>
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
            background: linear-gradient(135deg, #1e40af, #3b82f6);
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
        .ticket-info {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #10b981;
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
            background: #10b981;
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
        .important-note {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .qr-code {
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎉 Booking Confirmed!</h1>
        <p>Your ferry ticket is ready for your journey</p>
    </div>

    <div class="content">
        <div class="success-badge">✅ Payment Confirmed</div>
        
        <p>Dear {{ $booking->full_name }},</p>
        
        <p>Great news! Your booking has been confirmed and your payment has been successfully processed. Your ferry ticket is attached to this email.</p>

        <div class="ticket-info">
            <h3>🎫 Booking Details</h3>
            
            <div class="info-row">
                <span class="label">Booking Reference:</span>
                <span class="value"><strong>{{ $booking->payment_reference ?? 'BLT-' . str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</strong></span>
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
                <span class="label">Total Amount:</span>
                <span class="value"><strong>₱{{ number_format($booking->total_amount, 2) }}</strong></span>
            </div>
            
            <div class="info-row">
                <span class="label">Payment Method:</span>
                <span class="value">{{ ucfirst(str_replace('_', ' ', $booking->payment_method)) }}</span>
            </div>
        </div>

        <div class="important-note">
            <h4>📋 Important Reminders:</h4>
            <ul>
                <li><strong>Arrive 30 minutes before departure</strong> for check-in and boarding</li>
                <li>Bring a <strong>valid ID</strong> for verification</li>
                <li>Your ticket is attached as a PDF - please <strong>print or save it on your phone</strong></li>
                <li>Check weather conditions before traveling</li>
                <li>Contact us if you need to make changes to your booking</li>
            </ul>
        </div>

        <div class="qr-code">
            <p><strong>Show this email or your printed ticket at the terminal</strong></p>
            <p style="font-size: 12px; color: #6b7280;">Booking ID: {{ $booking->id }}</p>
        </div>

        <p>We're excited to have you aboard! If you have any questions or need assistance, please don't hesitate to contact our customer service team.</p>
        
        <p>Safe travels!</p>
        <p><strong>The BaltBep Ferry Team</strong></p>
    </div>

    <div class="footer">
        <h3>BaltBep Ferry Services</h3>
        <p>📧 support@baltbep.com | 📞 +63 123 456 7890</p>
        <p>🌐 www.baltbep.com</p>
        <p style="font-size: 12px; margin-top: 15px;">
            This is an automated email. Please do not reply to this message.
        </p>
    </div>
</body>
</html>