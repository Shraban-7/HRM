<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Department;
use App\Models\User;
use App\Helpers\HashHelper;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with(['user', 'department'])->paginate(15);
        return view('employee.index', compact('employees'));
    }

    public function create()
    {
        $departments = Department::all();
        return view('employee.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|unique:users,username',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'department_id' => 'required|exists:departments,id',
            'employee_id' => 'required|string|unique:employees,employee_id',
            'hire_date' => 'required|date',
            'designation' => 'required|string',
            'salary' => 'required|numeric|min:0',
        ]);

        $salt = HashHelper::generateSalt();
        $user = User::create([
            'email' => $request->input('email'),
            'username' => $request->input('username'),
            'password_hash' => HashHelper::hashPassword('changeme123', $salt),
            'salt' => $salt,
            'status' => 'active',
        ]);

        $employee = Employee::create([
            'user_id' => $user->id,
            'department_id' => $request->input('department_id'),
            'employee_id' => $request->input('employee_id'),
            'hire_date' => $request->input('hire_date'),
            'designation' => $request->input('designation'),
            'salary' => $request->input('salary'),
            'status' => 'active',
        ]);

        return redirect()->route('employees.index')
            ->with('success', 'Employee created successfully.');
    }

    public function show(Employee $employee)
    {
        $employee->load(['user', 'department', 'payslips', 'attendance', 'leaveRequests']);
        return view('employee.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $departments = Department::all();
        return view('employee.edit', compact('employee', 'departments'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'designation' => 'required|string',
            'salary' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,terminated',
        ]);

        $employee->update($request->only(['department_id', 'designation', 'salary', 'status']));

        return redirect()->route('employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $employee->update(['status' => 'terminated']);

        return redirect()->route('employees.index')
            ->with('success', 'Employee terminated successfully.');
    }
}