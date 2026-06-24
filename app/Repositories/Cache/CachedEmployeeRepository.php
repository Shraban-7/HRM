<?php

namespace App\Repositories\Cache;

use App\Models\Employee;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class CachedEmployeeRepository implements EmployeeRepositoryInterface
{
    protected EmployeeRepositoryInterface $repository;

    protected int $ttl;

    public function __construct(EmployeeRepositoryInterface $repository, int $ttl = 3600)
    {
        $this->repository = $repository;
        $this->ttl = $ttl;
    }

    public function findAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $cacheKey = 'employees:all:' . md5(serialize($filters) . '|' . $perPage);

        return Cache::remember($cacheKey, $this->ttl, function () use ($filters, $perPage) {
            return $this->repository->findAll($filters, $perPage);
        });
    }

    public function findById(int $id): ?Employee
    {
        return Cache::remember("employees:{$id}", $this->ttl, function () use ($id) {
            return $this->repository->findById($id);
        });
    }

    public function findByUserId(int $userId): ?Employee
    {
        return Cache::remember("employees:user:{$userId}", $this->ttl, function () use ($userId) {
            return $this->repository->findByUserId($userId);
        });
    }

    public function findByDepartment(int $departmentId): Collection
    {
        return Cache::remember("employees:department:{$departmentId}", $this->ttl, function () use ($departmentId) {
            return $this->repository->findByDepartment($departmentId);
        });
    }

    public function search(string $query): Collection
    {
        $cacheKey = 'employees:search:' . md5($query);

        return Cache::remember($cacheKey, $this->ttl / 2, function () use ($query) {
            return $this->repository->search($query);
        });
    }

    public function create(array $data): Employee
    {
        $employee = $this->repository->create($data);
        $this->clearCache();

        return $employee;
    }

    public function update(Employee $employee, array $data): Employee
    {
        $result = $this->repository->update($employee, $data);
        $this->clearCache($employee->id);

        return $result;
    }

    public function delete(Employee $employee): void
    {
        $this->repository->delete($employee);
        $this->clearCache($employee->id);
    }

    /**
     * Clear cache for employee-related keys.
     */
    protected function clearCache(?int $employeeId = null): void
    {
        Cache::forget('employees:all:*');

        if ($employeeId) {
            Cache::forget("employees:{$employeeId}");
        }
    }
}