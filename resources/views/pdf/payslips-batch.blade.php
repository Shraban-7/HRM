<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payroll Batch #{{ $payroll->id ?? 'N/A' }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; color: #333; margin: 0; padding: 15px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 15px; }
        .header h1 { margin: 0; font-size: 18px; }
        .payslip-page { page-break-after: always; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px dashed #ccc; }
        .payslip-page:last-child { page-break-after: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #ccc; padding: 5px 8px; text-align: left; font-size: 10px; }
        th { background: #f5f5f5; }
        .total { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>PAYROLL BATCH - #{{ $payroll->id ?? 'N/A' }}</h1>
        <p>Period: {{ $payroll->period_start ?? 'N/A' }} to {{ $payroll->period_end ?? 'N/A' }}</p>
        <p>Generated: {{ now()->format('M d, Y H:i') }}</p>
    </div>

    @foreach($payslips as $payslip)
    <div class="payslip-page">
        <h3>{{ $payslip->employee->user->username ?? 'N/A' }} ({{ $payslip->employee->employee_id ?? 'N/A' }})</h3>
        <p>{{ $payslip->employee->department->name ?? 'N/A' }} - {{ $payslip->employee->designation ?? 'N/A' }}</p>
        <table>
            <tr><th>Item</th><th>Amount</th></tr>
            <tr><td>Gross Salary</td><td>${{ number_format($payslip->gross, 2) }}</td></tr>
            <tr><td>Deductions</td><td>${{ number_format($payslip->deductions, 2) }}</td></tr>
            <tr><td class="total">Net Pay</td><td class="total">${{ number_format($payslip->net, 2) }}</td></tr>
        </table>
    </div>
    @endforeach
</body>
</html>