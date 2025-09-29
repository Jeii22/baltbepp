<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ferry Ticket</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f8fafc;
        }
        .ticket {
            background: white;
            max-width: 600px;
            margin: 0 auto;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .ticket-header {
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }
        .ticket-header::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            right: 0;
            height: 20px;
            background: white;
            border-radius: 50% 50% 0 0 / 100% 100% 0 0;
        }
        .company-logo {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .ticket-title {
            font-size: 18px;
            margin: 0;
            opacity: 0.9;
        }
        .ticket-body {
            padding: 40px 30px 30px;
        }
        .booking-ref {
            text-align: center;
            margin-bottom: 30px;
        }
        .booking-ref-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        .booking-ref-value {
            font-size: 24px;
            font-weight: bold;
            color: #1e40af;
            font-family: 'Courier New', monospace;
        }
        .route-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 30px 0;
            padding: 20px;
            background: #f1f5f9;
            border-radius: 10px;
        }
        .route-point {
            text-align: center;
            flex: 1;
        }
        .route-city {
            font-size: 20px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }
        .route-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
        }
        .route-arrow {
            margin: 0 20px;
            font-size: 24px;
            color: #3b82f6;
        }
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 30px 0;
        }
        .detail-item {
            padding: 15px;
            background: #f8fafc;
            border-radius: 8px;
            border-left: 4px solid #3b82f6;
        }
        .detail-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        .detail-value {
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
        }
        .passengers-section {
            margin: 30px 0;
            padding: 20px;
            background: #ecfdf5;
            border-radius: 10px;
            border: 1px solid #22c55e;
        }
        .passengers-title {
            font-size: 16px;
            font-weight: bold;
            color: #15803d;
            margin-bottom: 15px;
        }
        .passenger-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .passenger-badge {
            background: #22c55e;
            color: white;
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        .qr-section {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background: #f8fafc;
            border-radius: 10px;
        }
        .qr-placeholder {
            width: 120px;
            height: 120px;
            background: #e5e7eb;
            border: 2px dashed #9ca3af;
            border-radius: 10px;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: #6b7280;
            text-align: center;
        }
        .qr-instructions {
            font-size: 14px;
            color: #6b7280;
            margin-top: 10px;
        }
        .important-notes {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 10px;
            padding: 20px;
            margin: 30px 0;
        }
        .important-notes h4 {
            color: #92400e;
            margin: 0 0 15px 0;
            font-size: 16px;
        }
        .important-notes ul {
            margin: 0;
            padding-left: 20px;
            color: #92400e;
        }
        .important-notes li {
            margin-bottom: 8px;
            font-size: 14px;
        }
        .ticket-footer {
            background: #1f2937;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .footer-company {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .footer-contact {
            font-size: 14px;
            opacity: 0.8;
        }
        .generated-info {
            font-size: 10px;
            color: #9ca3af;
            text-align: center;
            margin-top: 20px;
        }
        .total-amount {
            text-align: center;
            margin: 20px 0;
            padding: 20px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border-radius: 10px;
        }
        .total-label {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 5px;
        }
        .total-value {
            font-size: 28px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="ticket-header">
            <div class="company-logo">🚢 BaltBep Ferry</div>
            <p class="ticket-title">Official Ferry Ticket</p>
        </div>

        <div class="ticket-body">
            <div class="booking-ref">
                <div class="booking-ref-label">Booking Reference</div>
                <div class="booking-ref-value">{{ $qrData }}</div>
            </div>

            <div class="route-section">
                <div class="route-point">
                    <div class="route-city">{{ $booking->origin }}</div>
                    <div class="route-label">From</div>
                </div>
                <div class="route-arrow">✈️</div>
                <div class="route-point">
                    <div class="route-city">{{ $booking->destination }}</div>
                    <div class="route-label">To</div>
                </div>
            </div>

            <div class="details-grid">
                <div class="detail-item">
                    <div class="detail-label">Departure Date</div>
                    <div class="detail-value">{{ $booking->departure_time->format('M j, Y') }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Departure Time</div>
                    <div class="detail-value">{{ $booking->departure_time->format('g:i A') }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Passenger Name</div>
                    <div class="detail-value">{{ $booking->full_name }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Payment Method</div>
                    <div class="detail-value">{{ ucfirst(str_replace('_', ' ', $booking->payment_method)) }}</div>
                </div>
            </div>

            <div class="passengers-section">
                <div class="passengers-title">👥 Passengers</div>
                <div class="passenger-list">
                    @if($booking->adult > 0)
                        <span class="passenger-badge">{{ $booking->adult }} Adult{{ $booking->adult > 1 ? 's' : '' }}</span>
                    @endif
                    @if($booking->child > 0)
                        <span class="passenger-badge">{{ $booking->child }} Child{{ $booking->child > 1 ? 'ren' : '' }}</span>
                    @endif
                    @if($booking->infant > 0)
                        <span class="passenger-badge">{{ $booking->infant }} Infant{{ $booking->infant > 1 ? 's' : '' }}</span>
                    @endif
                    @if($booking->pwd > 0)
                        <span class="passenger-badge">{{ $booking->pwd }} PWD/Senior{{ $booking->pwd > 1 ? 's' : '' }}</span>
                    @endif
                    @if($booking->student > 0)
                        <span class="passenger-badge">{{ $booking->student }} Student{{ $booking->student > 1 ? 's' : '' }}</span>
                    @endif
                </div>
            </div>

            <div class="total-amount">
                <div class="total-label">Total Amount Paid</div>
                <div class="total-value">₱{{ number_format($booking->total_amount, 2) }}</div>
            </div>

            <div class="qr-section">
                <div class="qr-placeholder">
                    QR Code<br>
                    {{ $qrData }}
                </div>
                <div class="qr-instructions">
                    <strong>Show this ticket at the terminal for boarding</strong>
                </div>
            </div>

            <div class="important-notes">
                <h4>📋 Important Reminders</h4>
                <ul>
                    <li><strong>Arrive 30 minutes before departure</strong> for check-in</li>
                    <li>Bring a <strong>valid government-issued ID</strong></li>
                    <li>This ticket is <strong>non-transferable</strong></li>
                    <li>Check weather conditions before traveling</li>
                    <li>Contact customer service for changes or cancellations</li>
                </ul>
            </div>
        </div>

        <div class="ticket-footer">
            <div class="footer-company">BaltBep Ferry Services</div>
            <div class="footer-contact">
                📧 support@baltbep.com | 📞 +63 123 456 7890<br>
                🌐 www.baltbep.com
            </div>
        </div>
    </div>

    <div class="generated-info">
        Ticket generated on {{ $generatedAt }} | Booking ID: {{ $booking->id }}
    </div>
</body>
</html>