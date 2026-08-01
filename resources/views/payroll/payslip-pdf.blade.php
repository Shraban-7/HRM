<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payslip #{{ $payslip->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 30px; color: #333; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #333; padding-bottom: 12px; margin-bottom: 24px; }
        h1 { font-size: 20px; margin: 0; }
        .company { color: #666; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        th { background: #f4f4f4; }
        .total td { font-weight: bold; background: #f4f4f4; }
        .amount { text-align: right; }
        .footer { margin-top: 40px; font-size: 11px; color: #999; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1>Payslip</h1>
            <div class="company">HRM System &middot; Payroll Period: {{ $payslip->payroll->period_start ?? 'N/A' }} to {{ $payslip->payroll->period_end ?? 'N/A' }}</div>
        </div>
        <div>
            <p style="margin:0">Payslip #{{ $payslip->id }}</p>
            <p style="margin:0" class="company">Issued: {{ $payslip->issued_at ? $payslip->issued_at->format('d M Y') : 'N/A' }}</p>
        </div>
    </div>

    <table>
        <tr>
            <th>Employee</th>
            <td>{{ $payslip->employee->full_name ?? $payslip->employee->user->username ?? 'N/A' }}</td>
            <th>Employee ID</th>
            <td>{{ $payslip->employee->employee_id ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Designation</th>
            <td>{{ $payslip->employee->designation ?? 'N/A' }}</td>
            <th>Department</th>
            <td>{{ $payslip->employee->department->name ?? 'N/A' }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <th>Description</th>
            <th class="amount">Amount</th>
        </tr>
        <tr>
            <td>Gross Pay</td>
            <td class="amount">${{ number_format($payslip->gross, 2) }}</td>
        </tr>
        <tr>
            <td>Deductions</td>
            <td class="amount">-${{ number_format($payslip->deductions, 2) }}</td>
        </tr>
        <tr class="total">
            <td>Net Pay</td>
            <td class="amount">${{ number_format($payslip->net, 2) }}</td>
        </tr>
    </table>

    <div class="footer">This is a system-generated payslip and does not require a signature.</div>
</body>
</html>
