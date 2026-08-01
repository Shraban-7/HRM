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
    <table id="payslips-table" class="w-full">
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
            <tr data-employee-id="{{ $payslip->employee_id }}" data-status="{{ $payslip->status ?? 'completed' }}">
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

@push('scripts')
<script>
$(document).ready(function() {
    // Populate employee filter from table data
    const $employeeFilter = $('#employee-filter');
    const $statusFilter = $('#status-filter');
    const $table = $('#payslips-table');
    
    $table.find('tbody tr').each(function() {
        const name = $(this).find('td:eq(1)').text().trim();
        if (name && !$employeeFilter.find('option[value="' + name + '"]').length) {
            $employeeFilter.append('<option value="' + name + '">' + name + '</option>');
        }
    });

    // Filter by employee name
    $employeeFilter.on('change', function() {
        const val = $(this).val();
        $table.DataTable().column(1).search(val).draw();
    });

    // Filter by status using data-status attribute
    $statusFilter.on('change', function() {
        const val = $(this).val();
        if (val) {
            $table.DataTable().column(6).search(val).draw();
        } else {
            $table.DataTable().column(6).search('').draw();
        }
    });
});
</script>
@endpush