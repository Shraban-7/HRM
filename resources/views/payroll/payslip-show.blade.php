@extends('layouts.app')
@section('title', 'Payslip #' . $payslip->id)
@section('content')
<div class="bg-white p-6 rounded-lg shadow max-w-3xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-semibold">Payslip</h3>
        <div class="space-x-2">
            <a href="{{ route('payslips.download', $payslip) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"><i class="fas fa-download mr-1"></i>PDF</a>
            <a href="{{ route('payslips.index') }}" class="text-gray-600 hover:text-gray-800"><i class="fas fa-arrow-left mr-1"></i>Back</a>
        </div>
    </div>

    <div class="border rounded-lg p-6">
        <div class="flex justify-between items-start border-b pb-4 mb-4">
            <div>
                <p class="font-semibold text-lg">{{ $payslip->employee->full_name ?? $payslip->employee->user->username ?? 'N/A' }}</p>
                <p class="text-sm text-gray-500">{{ $payslip->employee->designation ?? '' }} &middot; {{ $payslip->employee->employee_id ?? '' }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Period: {{ $payslip->payroll->period_start ?? 'N/A' }} to {{ $payslip->payroll->period_end ?? 'N/A' }}</p>
                <p class="text-sm text-gray-500">Issued: {{ $payslip->issued_at ? $payslip->issued_at->format('d M Y') : 'N/A' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4 text-center mb-6">
            <div class="bg-gray-50 p-4 rounded">
                <p class="text-sm text-gray-500">Gross Pay</p>
                <p class="text-lg font-semibold">${{ number_format($payslip->gross, 2) }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded">
                <p class="text-sm text-gray-500">Deductions</p>
                <p class="text-lg font-semibold text-red-600">-${{ number_format($payslip->deductions, 2) }}</p>
            </div>
            <div class="bg-green-50 p-4 rounded">
                <p class="text-sm text-gray-500">Net Pay</p>
                <p class="text-lg font-semibold text-green-700">${{ number_format($payslip->net, 2) }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
