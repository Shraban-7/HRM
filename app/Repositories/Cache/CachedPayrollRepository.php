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
        $cacheKey = $this->cacheKey('all:'.md5(serialize($filters).'|'.$perPage));

        return Cache::remember($cacheKey, $this->ttl, function () use ($filters, $perPage) {
            return $this->repository->findAll($filters, $perPage);
        });
    }

    public function findById(int $id): ?Payroll
    {
        return Cache::remember($this->cacheKey("id:{$id}"), $this->ttl, function () use ($id) {
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
        $this->clearCache();

        return $result;
    }

    public function getPayslipsForPayroll(int $payrollId): Collection
    {
        return Cache::remember($this->cacheKey("id:{$payrollId}:payslips"), $this->ttl, function () use ($payrollId) {
            return $this->repository->getPayslipsForPayroll($payrollId);
        });
    }

    /**
     * Build a version-prefixed cache key so invalidation can be performed
     * by bumping the namespace version rather than enumerating keys.
     */
    protected function cacheKey(string $suffix): string
    {
        $version = (int) Cache::get('payrolls:version', 1);

        return "payrolls:v{$version}:{$suffix}";
    }

    /**
     * Clear all payroll cache entries by bumping the namespace version.
     */
    protected function clearCache(): void
    {
        Cache::increment('payrolls:version');
    }
}
