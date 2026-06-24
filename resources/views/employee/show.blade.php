@extends('layouts.app')
@section('title', 'Employee Details - ' . $employee->employee_id)
@section('content')
<div class="space-y-6">
    <!-- Employee Info Header -->
    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">{{ $employee->user->username ?? 'N/A' }} ({{ $employee->employee_id }})</h3>
            <div class="space-x-2">
                <a href="{{ route('employees.edit', $employee) }}" class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600"><i class="fas fa-edit mr-1"></i>Edit</a>
                <a href="{{ route('employees.index') }}" class="text-gray-600 hover:text-gray-800"><i class="fas fa-arrow-left mr-1"></i>Back</a>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
            <div><span class="text-gray-500">Email:</span> {{ $employee->user->email ?? 'N/A' }}</div>
            <div><span class="text-gray-500">Department:</span> {{ $employee->department->name ?? 'N/A' }}</div>
            <div><span class="text-gray-500">Designation:</span> {{ $employee->designation }}</div>
            <div><span class="text-gray-500">Salary:</span> ${{ number_format($employee->salary, 2) }}</div>
            <div><span class="text-gray-500">Status:</span> <span class="px-2 py-1 rounded text-sm {{ $employee->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $employee->status }}</span></div>
            <div><span class="text-gray-500">Hire Date:</span> {{ $employee->hire_date }}</div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow">
        <div class="flex border-b">
            <button class="px-6 py-3 text-sm font-medium border-b-2 border-blue-600 text-blue-600" data-tab="payslips">Payslips</button>
            <button class="px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700" data-tab="attendance">Attendance</button>
            <button class="px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700" data-tab="leave">Leave Requests</button>
        </div>

        <!-- Payslips Tab -->
        <div class="p-6" id="tab-payslips">
            @if($employee->payslips->isEmpty())
                <p class="text-gray-500">No payslips found.</p>
            @else
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-sm text-gray-500 border-b">
                            <th class="pb-2">Payroll</th>
                            <th class="pb-2">Gross</th>
                            <th class="pb-2">Deductions</th>
                            <th class="pb-2">Net</th>
                            <th class="pb-2">Issued</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employee->payslips as $payslip)
                        <tr class="border-b">
                            <td class="py-2">Payroll #{{ $payslip->payroll_id }}</td>
                            <td class="py-2">${{ number_format($payslip->gross, 2) }}</td>
                            <td class="py-2">${{ number_format($payslip->deductions, 2) }}</td>
                            <td class="py-2">${{ number_format($payslip->net, 2) }}</td>
                            <td class="py-2">{{ $payslip->issued_at?->format('M d, Y') ?? 'N/A' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <!-- Attendance Tab -->
        <div class="p-6 hidden" id="tab-attendance">
            @if($employee->attendance->isEmpty())
                <p class="text-gray-500">No attendance records found.</p>
            @else
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-sm text-gray-500 border-b">
                            <th class="pb-2">Date</th>
                            <th class="pb-2">Clock In</th>
                            <th class="pb-2">Clock Out</th>
                            <th class="pb-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employee->attendance as $att)
                        <tr class="border-b">
                            <td class="py-2">{{ $att->date }}</td>
                            <td class="py-2">{{ $att->clock_in ? \Carbon\Carbon::parse($att->clock_in)->format('H:i') : 'N/A' }}</td>
                            <td class="py-2">{{ $att->clock_out ? \Carbon\Carbon::parse($att->clock_out)->format('H:i') : 'N/A' }}</td>
                            <td class="py-2">{{ ucfirst($att->status) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <!-- Leave Tab -->
        <div class="p-6 hidden" id="tab-leave">
            @if($employee->leaveRequests->isEmpty())
                <p class="text-gray-500">No leave requests found.</p>
            @else
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-sm text-gray-500 border-b">
                            <th class="pb-2">Type</th>
                            <th class="pb-2">Start</th>
                            <th class="pb-2">End</th>
                            <th class="pb-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employee->leaveRequests as $leave)
                        <tr class="border-b">
                            <td class="py-2">{{ ucfirst($leave->type) }}</td>
                            <td class="py-2">{{ $leave->start_date }}</td>
                            <td class="py-2">{{ $leave->end_date }}</td>
                            <td class="py-2"><span class="px-2 py-1 rounded text-sm {{ $leave->status === 'approved' ? 'bg-green-100 text-green-800' : ($leave->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">{{ ucfirst($leave->status) }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('[data-tab]').on('click', function() {
        const tab = $(this).data('tab');
        $('[data-tab]').removeClass('border-b-2 border-blue-600 text-blue-600').addClass('text-gray-500');
        $(this).addClass('border-b-2 border-blue-600 text-blue-600').removeClass('text-gray-500');
        $('div[id^="tab-"]').addClass('hidden');
        $('#tab-' + tab).removeClass('hidden');
    });
});
</script>
@endpush
@endsection