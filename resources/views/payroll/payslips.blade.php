@extends('layouts.app')
@section('title', 'Payslips')
@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Payslips</h3>
        <div class="space-x-2">
            <select id="employee-filter" class="border rounded px-3 py-1 text-sm">
                <option value="">All Employees</option>
            </select>
            <select id="status-filter" class="border rounded px-3 py-1 text-sm">
                <option value="">All Statuses</option>
                <option value="completed">Completed</option>
                <option value="pending">Pending</option>
            </select>
        </div>
    </div>
    <table id="payslips-table" class="w-full datatable">
        <thead>
            <tr class="text-left text-sm text-gray-500 border-b">
                <th class="pb-2">ID</th>
                <th class="pb-2">Employee</th>
                <th class="pb-2">Payroll</th>
                <th class="pb-2">Gross</th>
                <th class="pb-2">Deductions</th>
                <th class="pb-2">Net</th>
                <th class="pb-2">Issued</th>
                <th class="pb-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payslips ?? [] as $payslip)
            <tr data-employee-id="{{ $payslip->employee_id }}">
                <td class="py-2">{{ $payslip->id }}</td>
                <td class="py-2">{{ $payslip->employee->user->username ?? 'N/A' }}</td>
                <td class="py-2">#{{ $payslip->payroll_id }}</td>
                <td class="py-2">${{ number_format($payslip->gross, 2) }}</td>
                <td class="py-2">${{ number_format($payslip->deductions, 2) }}</td>
                <td class="py-2">${{ number_format($payslip->net, 2) }}</td>
                <td class="py-2">{{ $payslip->issued_at?->format('M d, Y') ?? 'N/A' }}</td>
                <td class="py-2">
                    <a href="{{ route('payslips.show', $payslip) }}" class="text-blue-600 hover:underline mr-2"><i class="fas fa-eye"></i></a>
                    <a href="{{ route('payslips.download', $payslip) }}" class="text-green-600 hover:underline"><i class="fas fa-download"></i></a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection