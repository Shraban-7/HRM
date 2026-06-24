<?php

namespace App\Repositories\Cache;

use App\Models\Payroll;
use App\Repositories\Contracts\PayrollRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class CachedPayrollRepository implements PayrollRepositoryInterface
{
    protected PayrollRepositoryInterface $repository;

    protected int $ttl;

    public function __construct(PayrollRepositoryInterface $repository, int $ttl = 3600)
    {
        $this->repository = $repository;
        $this->ttl = $ttl;
    }

    public function findAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $cacheKey = 'payrolls:all:' . md5(serialize($filters) . '|' . $perPage);

        return Cache::remember($cacheKey, $this->ttl, function () use ($filters, $perPage) {
            return $this->repository->findAll($filters, $perPage);
        });
    }

    public function findById(int $id): ?Payroll
    {
        return Cache::remember("payrolls:{$id}", $this->ttl, function () use ($id) {
            return $this->repository->findById($id);
        });
    }

    public function create(array $data): Payroll
    {
        $payroll = $this->repository->create($data);
        $this->clearCache();

        return $payroll;
    }

    public function update(Payroll $payroll, array $data): Payroll
    {
        $result = $this->repository->update($payroll, $data);
        $this->clearCache($payroll->id);

        return $result;
    }

    public function getPayslipsForPayroll(int $payrollId): Collection
    {
        return Cache::remember("payrolls:{$payrollId}:payslips", $this->ttl, function () use ($payrollId) {
            return $this->repository->getPayslipsForPayroll($payrollId);
        });
    }

    /**
     * Clear cache for payroll-related keys.
     */
    protected function clearCache(?int $payrollId = null): void
    {
        Cache::forget('payrolls:all:*');

        if ($payrollId) {
            Cache::forget("payrolls:{$payrollId}");
            Cache::forget("payrolls:{$payrollId}:payslips");
        }
    }
}