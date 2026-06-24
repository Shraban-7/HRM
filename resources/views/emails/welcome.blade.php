<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: Arial, sans-serif; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; border: 1px solid #ddd; padding: 30px; border-radius: 8px;">
        <h1 style="color: #2563eb; text-align: center;">Welcome to HRM System!</h1>
        <p>Hello <strong>{{ $name }}</strong>,</p>
        <p>Welcome aboard! Your account has been created successfully.</p>
        <p>Your login email is: <strong>{{ $email }}</strong></p>
        <p>Please use your temporary password to log in and change it immediately.</p>
        <p style="margin-top: 30px; text-align: center;">
            <a href="{{ url('/login') }}" style="background: #2563eb; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Log In Now</a>
        </p>
        <p style="color: #999; font-size: 12px; margin-top: 30px;">If you did not expect this email, please contact HR immediately.</p>
    </div>
</body>
</html>