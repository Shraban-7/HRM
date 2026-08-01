@extends('layouts.app')
@section('title', 'HR Dashboard')
@section('content')


<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-gray-500 text-sm">Total Employees</h3>
        <p class="text-3xl font-bold">{{ $totalEmployees }}</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-gray-500 text-sm">Active Employees</h3>
        <p class="text-3xl font-bold">{{ $activeEmployees }}</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-gray-500 text-sm">Departments</h3>
        <p class="text-3xl font-bold">{{ $totalDepartments }}</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-gray-500 text-sm">Pending Leaves</h3>
        <p class="text-3xl font-bold">{{ $pendingLeaves }}</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-gray-500 text-sm">Active Payrolls</h3>
        <p class="text-3xl font-bold">{{ $activePayrolls }}</p>
    </div>
</div>
<div class="bg-white p-6 rounded-lg shadow">
    <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('employees.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded text-center hover:bg-blue-700">Add Employee</a>
        <a href="{{ route('payrolls.create') }}" class="px-4 py-2 bg-green-600 text-white rounded text-center hover:bg-green-700">Run Payroll</a>
        <a href="{{ route('jobs.create') }}" class="px-4 py-2 bg-purple-600 text-white rounded text-center hover:bg-purple-700">Post Job</a>
        <a href="{{ route('audit.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded text-center hover:bg-gray-700">View Audit</a>
    </div>
</div>
@endsection