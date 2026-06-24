<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: Arial, sans-serif; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; border: 1px solid #ddd; padding: 30px; border-radius: 8px;">
        <h1 style="color: #2563eb; text-align: center;">Your Payslip is Ready</h1>
        <p>Hello <strong>{{ $name }}</strong>,</p>
        <p>Your payslip for the period <strong>{{ $period_start }} to {{ $period_end }}</strong> has been processed.</p>
        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <tr><td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Net Pay</td><td style="padding: 8px; border: 1px solid #ddd;">${{ number_format($net, 2) }}</td></tr>
        </table>
        <p style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/payslips') }}" style="background: #2563eb; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">View Payslip</a>
        </p>
        <p style="color: #999; font-size: 12px;">If you have questions about your payslip, please contact the payroll department.</p>
    </div>
</body>
</html>