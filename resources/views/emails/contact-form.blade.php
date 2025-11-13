<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form Submission</title>
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
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        .content {
            background: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
        }
        .field {
            margin-bottom: 20px;
        }
        .label {
            font-weight: bold;
            color: #1f2937;
            display: block;
            margin-bottom: 5px;
        }
        .value {
            color: #4b5563;
            padding: 10px;
            background: white;
            border-radius: 4px;
            border: 1px solid #e5e7eb;
        }
        .message-box {
            background: white;
            padding: 15px;
            border-left: 4px solid #2563eb;
            border-radius: 4px;
            margin-top: 10px;
        }
        .footer {
            background: #1f2937;
            color: #9ca3af;
            padding: 15px;
            text-align: center;
            font-size: 12px;
            border-radius: 0 0 8px 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0;">New Contact Form Submission</h1>
        <p style="margin: 10px 0 0 0;">BaltBep Ferry Booking</p>
    </div>
    
    <div class="content">
        <div class="field">
            <span class="label">From:</span>
            <div class="value">{{ $contactData['first_name'] }} {{ $contactData['last_name'] }}</div>
        </div>
        
        <div class="field">
            <span class="label">Email Address:</span>
            <div class="value">{{ $contactData['email'] }}</div>
        </div>
        
        <div class="field">
            <span class="label">Subject:</span>
            <div class="value">{{ $contactData['subject'] }}</div>
        </div>
        
        <div class="field">
            <span class="label">Message:</span>
            <div class="message-box">
                {{ $contactData['message'] }}
            </div>
        </div>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #e5e7eb;">
            <p style="color: #6b7280; font-size: 14px; margin: 0;">
                <strong>💡 Tip:</strong> You can reply directly to this email to respond to {{ $contactData['first_name'] }}.
            </p>
        </div>
    </div>
    
    <div class="footer">
        <p style="margin: 0;">This message was sent from the BaltBep contact form</p>
        <p style="margin: 5px 0 0 0;">{{ now()->format('F d, Y \a\t h:i A') }}</p>
    </div>
</body>
</html>
