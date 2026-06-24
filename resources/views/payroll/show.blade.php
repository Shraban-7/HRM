@extends('layouts.app')
@section('title', 'Payroll #' . $payroll->id)
@section('content')
<div class="space-y-6">
    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Payroll #{{ $payroll->id }}</h3>
            <div class="space-x-2">
                <a href="{{ route('payrolls.calculate', $payroll) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"><i class="fas fa-calculator mr-1"></i>Calculate</a>
                <a href="{{ route('payrolls.index') }}" class="text-gray-600 hover:text-gray-800"><i class="fas fa-arrow-left mr-1"></i>Back</a>
            </div>
        </div>
        <div class="grid grid-cols-4 gap-4 text-sm">
            <div><span class="text-gray-500">Period:</span> {{ $payroll->period_start }} to {{ $payroll->period_end }}</div>
            <div><span class="text-gray-500">Status:</span> <span class="px-2 py-1 rounded text-sm {{ $payroll->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">{{ ucfirst($payroll->status) }}</span></div>
            <div><span class="text-gray-500">Total:</span> ${{ number_format($payroll->total_amount, 2) }}</div>
            <div><span class="text-gray-500">Employees:</span> {{ $payroll->payslips->count() }}</div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow">
        <h4 class="text-md font-semibold mb-4">Payslips</h4>
        @if($payroll->payslips->isEmpty())
            <p class="text-gray-500">No payslips generated yet. Click "Calculate" to process this payroll.</p>
        @else
            <table class="w-full">
                <thead>
                    <tr class="text-left text-sm text-gray-500 border-b">
                        <th class="pb-2">Employee</th>
                        <th class="pb-2">Gross</th>
                        <th class="pb-2">Deductions</th>
                        <th class="pb-2">Net</th>
                        <th class="pb-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payroll->payslips as $payslip)
                    <tr class="border-b">
                        <td class="py-2">{{ $payslip->employee->user->username ?? 'N/A' }}</td>
                        <td class="py-2">${{ number_format($payslip->gross, 2) }}</td>
                        <td class="py-2">${{ number_format($payslip->deductions, 2) }}</td>
                        <td class="py-2">${{ number_format($payslip->net, 2) }}</td>
                        <td class="py-2">
                            <a href="{{ route('payslips.show', $payslip) }}" class="text-blue-600 hover:underline mr-2"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('payslips.download', $payslip) }}" class="text-green-600 hover:underline"><i class="fas fa-download"></i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection