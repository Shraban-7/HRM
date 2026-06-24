<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: Arial, sans-serif; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; border: 1px solid #ddd; padding: 30px; border-radius: 8px;">
        <h1 style="color: {{ $status === 'approved' ? '#16a34a' : '#dc2626' }}; text-align: center;">Leave Request {{ ucfirst($status) }}</h1>
        <p>Hello <strong>{{ $name }}</strong>,</p>
        <p>Your leave request has been <strong>{{ $status }}</strong>.</p>
        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <tr><td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Type</td><td style="padding: 8px; border: 1px solid #ddd;">{{ ucfirst($type) }}</td></tr>
            <tr><td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Start Date</td><td style="padding: 8px; border: 1px solid #ddd;">{{ $start_date }}</td></tr>
            <tr><td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">End Date</td><td style="padding: 8px; border: 1px solid #ddd;">{{ $end_date }}</td></tr>
        </table>
        <p style="color: #999; font-size: 12px;">If you have questions, please contact your manager or HR.</p>
    </div>
</body>
</html>