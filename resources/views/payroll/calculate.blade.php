@extends('layouts.app')
@section('title', 'Calculate Payroll')
@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Calculate Payroll</h3>
        <a href="{{ route('payrolls.index') }}" class="text-gray-600 hover:text-gray-800"><i class="fas fa-arrow-left mr-1"></i>Back</a>
    </div>

    @if(isset($payroll))
    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded">
        <p class="text-blue-800">Payroll #{{ $payroll->id }} | Period: {{ $payroll->period_start }} to {{ $payroll->period_end }}</p>
        <p class="text-sm text-blue-600 mt-1">Status: {{ ucfirst($payroll->status) }} | Total: ${{ number_format($payroll->total_amount, 2) }}</p>
    </div>

    <div class="mb-4">
        <p class="text-gray-600">Click the button below to calculate payslips for all active employees for this payroll period.</p>
    </div>

    <form action="{{ route('payrolls.calculate', $payroll) }}" method="POST" id="payroll-calc-form">
        @csrf
        <button type="submit" id="calculate-payroll" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700" data-period="{{ $payroll->period_start }} to {{ $payroll->period_end }}">
            <i class="fas fa-calculator mr-2"></i>Calculate Payroll
        </button>
    </form>
    @else
    <p class="text-gray-500">Payroll period not found.</p>
    @endif
</div>
@endsection