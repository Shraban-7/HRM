<?php

namespace App\Repositories\Contracts;

use App\Models\Payroll;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface PayrollRepositoryInterface
{
    public function findAll(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findById(int $id): ?Payroll;
    public function create(array $data): Payroll;
    public function update(Payroll $payroll, array $data): Payroll;
    public function getPayslipsForPayroll(int $payrollId): Collection;
}