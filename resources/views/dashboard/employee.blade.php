@extends('layouts.app')
@section('title', 'Employee Dashboard')
@section('content')
<div class="space-y-6">
    <h2 class="text-xl font-semibold">Welcome, {{ session('user_name', 'Employee') }}!</h2>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-lg shadow">
            <h4 class="text-sm text-gray-500">My Attendance (This Month)</h4>
            <p class="text-2xl font-bold text-blue-600">{{ $attendanceCount ?? 0 }} days</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <h4 class="text-sm text-gray-500">Leave Balance</h4>
            <p class="text-2xl font-bold text-green-600">{{ $leaveBalance ?? 'N/A' }} days remaining</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <h4 class="text-sm text-gray-500">Recent Payslip</h4>
            <p class="text-2xl font-bold text-purple-600">${{ $recentPayslip ?? '0.00' }}</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('attendance.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"><i class="fas fa-clock mr-2"></i>Attendance</a>
            <a href="{{ route('leave.index') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"><i class="fas fa-calendar-alt mr-2"></i>Leave Requests</a>
            <a href="{{ route('payslips.index') }}" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700"><i class="fas fa-file-invoice mr-2"></i>My Payslips</a>
            <a href="{{ route('goals.index') }}" class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700"><i class="fas fa-bullseye mr-2"></i>My Goals</a>
        </div>
    </div>
</div>
@endsection