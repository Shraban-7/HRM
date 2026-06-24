<?php

namespace App\Repositories\Contracts;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface EmployeeRepositoryInterface
{
    public function findAll(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findById(int $id): ?Employee;
    public function findByUserId(int $userId): ?Employee;
    public function findByDepartment(int $departmentId): Collection;
    public function search(string $query): Collection;
    public function create(array $data): Employee;
    public function update(Employee $employee, array $data): Employee;
    public function delete(Employee $employee): void;
}