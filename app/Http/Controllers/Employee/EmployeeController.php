<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeBankDetail;
use App\Services\Employee\EmployeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    protected EmployeeService $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
        $this->authorizeResource(Employee::class, 'employee');
    }

    public function index()
    {
        $employees = Employee::with(['user', 'department'])->paginate(15);

        return view('employee.index', compact('employees'));
    }

    public function create()
    {
        $departments = Department::all();
        $managers = Employee::with('user')->where('status', 'active')->get();

        return view('employee.create', compact('departments', 'managers'));
    }

    public function store(StoreEmployeeRequest $request)
    {
        $validated = $request->validated();

        $userData = [
            'email' => $validated['email'],
            'username' => $validated['username'],
            'status' => 'active',
        ];

        $employeeData = $request->only([
            'department_id', 'employee_id', 'hire_date', 'designation', 'salary',
            'first_name', 'last_name', 'phone', 'date_of_birth', 'gender',
            'marital_status', 'blood_group', 'address', 'employment_type',
            'manager_id',
        ]);

        if ($request->hasFile('profile_photo')) {
            $employeeData['profile_photo_path'] = $request->file('profile_photo')->store('profile_photos', 'public');
        }

        DB::transaction(function () use ($userData, $employeeData, $validated) {
            $employee = $this->employeeService->createEmployee($userData, $employeeData);

            if (! empty($validated['bank_name']) || ! empty($validated['account_number'])) {
                EmployeeBankDetail::create([
                    'employee_id' => $employee->id,
                    'bank_name' => $validated['bank_name'] ?? null,
                    'account_name' => $validated['account_name'] ?? null,
                    'account_number' => $validated['account_number'] ?? null,
                    'routing_number' => $validated['routing_number'] ?? null,
                    'swift_code' => $validated['swift_code'] ?? null,
                    'tax_id' => $validated['tax_id'] ?? null,
                ]);
            }
        });

        return redirect()->route('employees.index')
            ->with('success', 'Employee created successfully.');
    }

    public function show(Employee $employee)
    {
        $this->authorize('view', $employee);
        $employee->load(['user', 'department', 'payslips', 'attendance', 'leaveRequests', 'bankDetail', 'emergencyContacts', 'manager']);

        return view('employee.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $departments = Department::all();
        $managers = Employee::with('user')->where('status', 'active')->where('id', '!=', $employee->id)->get();

        return view('employee.edit', compact('employee', 'departments', 'managers'));
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        $validated = $request->validated();

        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo_path'] = $request->file('profile_photo')->store('profile_photos', 'public');
        }

        $this->employeeService->updateEmployee($employee, $validated);

        $bankData = $request->only(['bank_name', 'account_name', 'account_number', 'routing_number', 'swift_code', 'tax_id']);

        if (array_filter($bankData)) {
            if ($employee->bankDetail) {
                $employee->bankDetail->update($bankData);
            } else {
                EmployeeBankDetail::create($bankData + ['employee_id' => $employee->id]);
            }
        }

        return redirect()->route('employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $this->employeeService->terminateEmployee($employee);

        return redirect()->route('employees.index')
            ->with('success', 'Employee terminated successfully.');
    }

    public function search(Request $request)
    {
        $query = trim((string) $request->query('q', ''));

        if ($query === '') {
            return response()->json([]);
        }

        $employees = $this->employeeService->searchEmployees($query);

        return response()->json($employees->map(fn ($employee) => [
            'id' => $employee->id,
            'name' => trim(($employee->first_name ?? '').' '.($employee->last_name ?? '')),
            'username' => $employee->user?->username,
            'email' => $employee->user?->email,
            'employee_id' => $employee->employee_id,
            'designation' => $employee->designation,
            'department' => $employee->department?->name,
        ]));
    }
}
