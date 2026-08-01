<?php

namespace App\Services\Employee;

use App\Events\EmployeeHired;
use App\Events\EmployeeTerminated;
use App\Events\SalaryChanged;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeService
{
    /**
     * Create a new employee with associated user account.
     */
    public function createEmployee(array $userData, array $employeeData): Employee
    {
        return DB::transaction(function () use ($userData, $employeeData) {
            $password = $userData['password'] ?? 'changeme123';

            $user = User::create([
                'email' => $userData['email'],
                'username' => $userData['username'],
                'password' => Hash::make($password),
                'status' => $userData['status'] ?? 'active',
            ]);

            $employee = Employee::create([
                'user_id' => $user->id,
                'department_id' => $employeeData['department_id'],
                'employee_id' => $employeeData['employee_id'],
                'hire_date' => $employeeData['hire_date'],
                'designation' => $employeeData['designation'],
                'salary' => $employeeData['salary'],
                'status' => $employeeData['status'] ?? 'active',
                'profile_photo_path' => $employeeData['profile_photo_path'] ?? null,
                'first_name' => $employeeData['first_name'] ?? null,
                'last_name' => $employeeData['last_name'] ?? null,
                'phone' => $employeeData['phone'] ?? null,
                'date_of_birth' => $employeeData['date_of_birth'] ?? null,
                'gender' => $employeeData['gender'] ?? null,
                'marital_status' => $employeeData['marital_status'] ?? null,
                'blood_group' => $employeeData['blood_group'] ?? null,
                'address' => $employeeData['address'] ?? null,
                'employment_type' => $employeeData['employment_type'] ?? 'full_time',
                'probation_ends_at' => $employeeData['probation_ends_at'] ?? null,
                'manager_id' => $employeeData['manager_id'] ?? null,
                'branch_id' => $employeeData['branch_id'] ?? null,
            ]);

            event(new EmployeeHired($employee));

            return $employee;
        });
    }

    /**
     * Update an existing employee.
     */
    public function updateEmployee(Employee $employee, array $data): Employee
    {
        return DB::transaction(function () use ($employee, $data) {
            $oldSalary = $employee->salary;

            $employee->update([
                'department_id' => $data['department_id'] ?? $employee->department_id,
                'designation' => $data['designation'] ?? $employee->designation,
                'salary' => $data['salary'] ?? $employee->salary,
                'status' => $data['status'] ?? $employee->status,
                'profile_photo_path' => $data['profile_photo_path'] ?? $employee->profile_photo_path,
                'first_name' => $data['first_name'] ?? $employee->first_name,
                'last_name' => $data['last_name'] ?? $employee->last_name,
                'phone' => $data['phone'] ?? $employee->phone,
                'date_of_birth' => $data['date_of_birth'] ?? $employee->date_of_birth,
                'gender' => $data['gender'] ?? $employee->gender,
                'marital_status' => $data['marital_status'] ?? $employee->marital_status,
                'blood_group' => $data['blood_group'] ?? $employee->blood_group,
                'address' => $data['address'] ?? $employee->address,
                'employment_type' => $data['employment_type'] ?? $employee->employment_type,
                'probation_ends_at' => $data['probation_ends_at'] ?? $employee->probation_ends_at,
                'manager_id' => $data['manager_id'] ?? $employee->manager_id,
                'branch_id' => $data['branch_id'] ?? $employee->branch_id,
            ]);

            // Fire SalaryChanged event if salary changed
            if (isset($data['salary']) && (float) $data['salary'] !== (float) $oldSalary) {
                event(new SalaryChanged($employee, (float) $oldSalary, (float) $data['salary']));
            }

            return $employee->fresh();
        });
    }

    /**
     * Terminate an employee.
     */
    public function terminateEmployee(Employee $employee): void
    {
        DB::transaction(function () use ($employee) {
            $employee->update(['status' => 'terminated']);
            event(new EmployeeTerminated($employee));
        });
    }

    /**
     * Get employee with all related details loaded.
     */
    public function getEmployeeWithDetails(Employee $employee): Employee
    {
        return $employee->load([
            'user',
            'department',
            'payslips' => fn ($q) => $q->latest(),
            'attendance' => fn ($q) => $q->latest()->limit(30),
            'leaveRequests' => fn ($q) => $q->latest(),
            'performanceReviews' => fn ($q) => $q->latest(),
            'goals',
            'benefitEnrollments.benefit',
        ]);
    }

    /**
     * Search employees by name, ID, or designation.
     */
    public function searchEmployees(string $query): Collection
    {
        return Employee::with('user', 'department')
            ->whereHas('user', function ($q) use ($query) {
                $q->where('username', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            })
            ->orWhere('employee_id', 'like', "%{$query}%")
            ->orWhere('designation', 'like', "%{$query}%")
            ->get();
    }
}
