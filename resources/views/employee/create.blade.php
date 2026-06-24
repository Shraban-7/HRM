@extends('layouts.app')
@section('title', 'Add Employee')
@section('content')
<div class="bg-white p-6 rounded-lg shadow max-w-2xl mx-auto">
    <h3 class="text-lg font-semibold mb-4">Add New Employee</h3>
    <form method="POST" action="{{ route('employees.store') }}">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div class="mb-4"><label class="block text-gray-700 mb-2">Email</label><input type="email" name="email" class="w-full px-3 py-2 border rounded" required></div>
            <div class="mb-4"><label class="block text-gray-700 mb-2">Username</label><input type="text" name="username" class="w-full px-3 py-2 border rounded" required></div>
            <div class="mb-4"><label class="block text-gray-700 mb-2">Employee ID</label><input type="text" name="employee_id" class="w-full px-3 py-2 border rounded" required></div>
            <div class="mb-4"><label class="block text-gray-700 mb-2">Department</label>
                <select name="department_id" class="w-full px-3 py-2 border rounded" required>
                    @foreach($departments as $dept)<option value="{{ $dept->id }}">{{ $dept->name }}</option>@endforeach
                </select>
            </div>
            <div class="mb-4"><label class="block text-gray-700 mb-2">Designation</label><input type="text" name="designation" class="w-full px-3 py-2 border rounded" required></div>
            <div class="mb-4"><label class="block text-gray-700 mb-2">Salary</label><input type="number" step="0.01" name="salary" class="w-full px-3 py-2 border rounded" required></div>
            <div class="mb-4"><label class="block text-gray-700 mb-2">Hire Date</label><input type="date" name="hire_date" class="w-full px-3 py-2 border rounded" required></div>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Create Employee</button>
    </form>
</div>
@endsection