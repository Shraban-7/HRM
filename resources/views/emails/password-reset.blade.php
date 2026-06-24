<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: Arial, sans-serif; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; border: 1px solid #ddd; padding: 30px; border-radius: 8px;">
        <h1 style="color: #dc2626; text-align: center;">Password Reset Request</h1>
        <p>Hello <strong>{{ $name }}</strong>,</p>
        <p>You have requested to reset your password. Click the button below to proceed:</p>
        <p style="text-align: center; margin: 30px 0;">
            <a href="{{ $resetUrl }}" style="background: #dc2626; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Reset Password</a>
        </p>
        <p style="color: #999; font-size: 12px;">This link will expire in 60 minutes.</p>
        <p style="color: #999; font-size: 12px;">If you did not request this, please ignore this email.</p>
    </div>
</body>
</html>