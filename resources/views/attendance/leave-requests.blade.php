@extends('layouts.app')
@section('title', 'Leave Requests')
@section('content')
<div class="space-y-6">
    <!-- Leave Request Form -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Submit Leave Request</h3>
        <form id="leave-request-form" action="{{ route('leave.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                    <select name="type" id="type" class="w-full border border-gray-300 rounded px-3 py-2" required>
                        <option value="">Select Type</option>
                        <option value="annual">Annual</option>
                        <option value="sick">Sick</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date *</label>
                    <input type="date" name="start_date" id="start_date" class="w-full border border-gray-300 rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date *</label>
                    <input type="date" name="end_date" id="end_date" class="w-full border border-gray-300 rounded px-3 py-2" required>
                </div>
            </div>
            <div class="flex justify-between items-center">
                <span id="leave-days-count" class="text-sm text-gray-500">Select dates to calculate days</span>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Submit Request</button>
            </div>
            @if($errors->any())
                <div class="text-red-500 text-sm">
                    @foreach($errors->all() as $error) <p>{{ $error }}</p> @endforeach
                </div>
            @endif
        </form>
    </div>

    <!-- Leave Requests List -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Leave Requests</h3>
        <table id="leave-requests-table" class="w-full datatable">
            <thead>
                <tr class="text-left text-sm text-gray-500 border-b">
                    <th class="pb-2">ID</th>
                    <th class="pb-2">Employee</th>
                    <th class="pb-2">Type</th>
                    <th class="pb-2">Start</th>
                    <th class="pb-2">End</th>
                    <th class="pb-2">Status</th>
                    <th class="pb-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($leaveRequests ?? [] as $leave)
                <tr>
                    <td class="py-2">{{ $leave->id }}</td>
                    <td class="py-2">{{ $leave->employee->user->username ?? 'N/A' }}</td>
                    <td class="py-2">{{ ucfirst($leave->type) }}</td>
                    <td class="py-2">{{ $leave->start_date }}</td>
                    <td class="py-2">{{ $leave->end_date }}</td>
                    <td class="py-2"><span class="px-2 py-1 rounded text-sm {{ $leave->status === 'approved' ? 'bg-green-100 text-green-800' : ($leave->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">{{ ucfirst($leave->status) }}</span></td>
                    <td class="py-2">
                        @if($leave->status === 'pending')
                            <form action="{{ route('leave.approve', $leave) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-green-600 hover:underline mr-1"><i class="fas fa-check"></i></button>
                            </form>
                            <form action="{{ route('leave.reject', $leave) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-red-600 hover:underline"><i class="fas fa-times"></i></button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection