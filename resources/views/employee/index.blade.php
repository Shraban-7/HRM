@extends('layouts.app')
@section('title', 'Employees')
@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">All Employees</h3>
        <a href="{{ route('employees.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"><i class="fas fa-plus mr-2"></i>Add Employee</a>
    </div>
    <table class="w-full datatable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Employee ID</th>
                <th>Department</th>
                <th>Designation</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $emp)
            <tr>
                <td>{{ $emp->id }}</td>
                <td>{{ $emp->user->username ?? 'N/A' }}</td>
                <td>{{ $emp->employee_id }}</td>
                <td>{{ $emp->department->name ?? 'N/A' }}</td>
                <td>{{ $emp->designation }}</td>
                <td><span class="px-2 py-1 rounded text-sm {{ $emp->status === 'active' ? 'bg-green-100 text-green-800' : ($emp->status === 'inactive' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">{{ $emp->status }}</span></td>
                <td>
                    <a href="{{ route('employees.show', $emp) }}" class="text-blue-600 hover:underline mr-2"><i class="fas fa-eye"></i></a>
                    <a href="{{ route('employees.edit', $emp) }}" class="text-yellow-600 hover:underline mr-2"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('employees.destroy', $emp) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
@endsection