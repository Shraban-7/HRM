<?php

namespace App\Repositories\Eloquent;

use App\Models\Employee;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class EmployeeRepository implements EmployeeRepositoryInterface
{
    public function findAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Employee::with(['user', 'department']);

        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('username', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhere('employee_id', 'like', "%{$search}%")
                ->orWhere('designation', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('id', 'desc')->paginate($perPage);
    }

    public function findById(int $id): ?Employee
    {
        return Employee::with(['user', 'department'])->find($id);
    }

    public function findByUserId(int $userId): ?Employee
    {
        return Employee::with(['user', 'department'])->where('user_id', $userId)->first();
    }

    public function findByDepartment(int $departmentId): Collection
    {
        return Employee::with(['user', 'department'])
            ->where('department_id', $departmentId)
            ->where('status', 'active')
            ->get();
    }

    public function search(string $query): Collection
    {
        return Employee::with(['user', 'department'])
            ->where(function ($q) use ($query) {
                $q->whereHas('user', function ($uq) use ($query) {
                    $uq->where('username', 'like', "%{$query}%")
                       ->orWhere('email', 'like', "%{$query}%");
                })
                ->orWhere('employee_id', 'like', "%{$query}%")
                ->orWhere('designation', 'like', "%{$query}%");
            })
            ->limit(20)
            ->get();
    }

    public function create(array $data): Employee
    {
        return Employee::create($data);
    }

    public function update(Employee $employee, array $data): Employee
    {
        $employee->update($data);
        return $employee->fresh(['user', 'department']);
    }

    public function delete(Employee $employee): void
    {
        $employee->update(['status' => 'terminated']);
    }
}