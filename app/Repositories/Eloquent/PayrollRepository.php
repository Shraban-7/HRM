<?php

namespace App\Repositories\Eloquent;

use App\Models\Payroll;
use App\Repositories\Contracts\PayrollRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PayrollRepository implements PayrollRepositoryInterface
{
    public function findAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Payroll::with('payslips');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['period_start'])) {
            $query->where('period_start', '>=', $filters['period_start']);
        }

        if (!empty($filters['period_end'])) {
            $query->where('period_end', '<=', $filters['period_end']);
        }

        return $query->orderBy('id', 'desc')->paginate($perPage);
    }

    public function findById(int $id): ?Payroll
    {
        return Payroll::with('payslips.employee.user')->find($id);
    }

    public function create(array $data): Payroll
    {
        return Payroll::create($data);
    }

    public function update(Payroll $payroll, array $data): Payroll
    {
        $payroll->update($data);
        return $payroll->fresh('payslips.employee.user');
    }

    public function getPayslipsForPayroll(int $payrollId): Collection
    {
        $payroll = Payroll::with('payslips.employee.user')->findOrFail($payrollId);
        return $payroll->payslips;
    }
}