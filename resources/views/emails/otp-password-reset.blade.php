<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Password Reset OTP</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px;">
        <h2 style="color: #333; margin-bottom: 20px;">Password Reset Request</h2>

        <p>Hello,</p>

        <p>You have requested to reset your password. Please use the following OTP (One-Time Password) to proceed with the password reset:</p>

        <div style="background-color: #fff; border: 2px solid #007bff; border-radius: 4px; padding: 15px; text-align: center; margin: 20px 0; font-size: 24px; font-weight: bold; letter-spacing: 5px;">
            {{ $otp }}
        </div>

        <p><strong>Important:</strong></p>
        <ul>
            <li>This OTP will expire in 10 minutes.</li>
            <li>Do not share this OTP with anyone.</li>
            <li>If you didn't request this password reset, please ignore this email.</li>
        </ul>

        <p>If you have any questions, please contact our support team.</p>

        <p>Best regards,<br>
        BaltBep Team</p>
    </div>
</body>
</html>