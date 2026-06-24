<?php

namespace App\Services\Employee;

use App\Models\Employee;
use App\Models\User;
use App\Helpers\HashHelper;
use App\Events\EmployeeHired;
use App\Events\EmployeeTerminated;
use App\Events\SalaryChanged;
use Illuminate\Support\Facades\DB;

class EmployeeService
{
    /**
     * Create a new employee with associated user account.
     */
    public function createEmployee(array $userData, array $employeeData): Employee
    {
        return DB::transaction(function () use ($userData, $employeeData) {
            $salt = HashHelper::generateSalt();
            $password = $userData['password'] ?? 'changeme123';

            $user = User::create([
                'email' => $userData['email'],
                'username' => $userData['username'],
                'password_hash' => HashHelper::hashPassword($password, $salt),
                'salt' => $salt,
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
            ]);

            // Fire SalaryChanged event if salary changed
            if (isset($data['salary']) && (float)$data['salary'] !== (float)$oldSalary) {
                event(new SalaryChanged($employee, (float)$oldSalary, (float)$data['salary']));
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
            'payslips' => fn($q) => $q->latest(),
            'attendance' => fn($q) => $q->latest()->limit(30),
            'leaveRequests' => fn($q) => $q->latest(),
            'performanceReviews' => fn($q) => $q->latest(),
            'goals',
            'benefitEnrollments.benefit',
        ]);
    }

    /**
     * Search employees by name, ID, or designation.
     */
    public function searchEmployees(string $query): \Illuminate\Database\Eloquent\Collection
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