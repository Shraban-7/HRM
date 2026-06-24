<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payslip - {{ $employee->employee_id }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; margin: 0; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 20px; }
        .header p { margin: 5px 0 0; font-size: 12px; color: #666; }
        .info { margin-bottom: 20px; }
        .info table { width: 100%; }
        .info td { padding: 3px 0; }
        .info .label { width: 150px; font-weight: bold; }
        table.details { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.details th, table.details td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        table.details th { background: #f5f5f5; }
        .total { font-size: 14px; font-weight: bold; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>PAYSLIP</h1>
        <p>Employee ID: {{ $employee->employee_id }}</p>
    </div>

    <div class="info">
        <table>
            <tr><td class="label">Employee Name:</td><td>{{ $employee->user->username ?? 'N/A' }}</td></tr>
            <tr><td class="label">Department:</td><td>{{ $employee->department->name ?? 'N/A' }}</td></tr>
            <tr><td class="label">Designation:</td><td>{{ $employee->designation }}</td></tr>
            <tr><td class="label">Payroll Period:</td><td>{{ $payroll->period_start }} to {{ $payroll->period_end }}</td></tr>
            <tr><td class="label">Issued At:</td><td>{{ $payslip->issued_at?->format('M d, Y') ?? 'Pending' }}</td></tr>
        </table>
    </div>

    <table class="details">
        <tr><th>Earnings</th><th>Amount</th></tr>
        <tr><td>Gross Salary</td><td>${{ number_format($payslip->gross, 2) }}</td></tr>
    </table>

    <table class="details" style="margin-top: 10px;">
        <tr><th>Deductions</th><th>Amount</th></tr>
        <tr><td>Total Deductions</td><td>${{ number_format($payslip->deductions, 2) }}</td></tr>
    </table>

    <table class="details" style="margin-top: 10px;">
        <tr><th>Summary</th><th>Amount</th></tr>
        <tr><td class="total">Net Pay</td><td class="total">${{ number_format($payslip->net, 2) }}</td></tr>
    </table>

    <div class="footer">
        <p>This is a computer-generated document. No signature is required.</p>
        <p>Generated on {{ now()->format('M d, Y H:i') }}</p>
    </div>
</body>
</html>