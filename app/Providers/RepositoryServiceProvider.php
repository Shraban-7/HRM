<?php

namespace App\Providers;

use App\Repositories\Cache\CachedEmployeeRepository;
use App\Repositories\Cache\CachedPayrollRepository;
use App\Repositories\Contracts\AttendanceRepositoryInterface;
use App\Repositories\Contracts\EmployeeRepositoryInterface;
use App\Repositories\Contracts\PayrollRepositoryInterface;
use App\Repositories\Eloquent\AttendanceRepository;
use App\Repositories\Eloquent\EmployeeRepository;
use App\Repositories\Eloquent\PayrollRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind Employee repository with optional cache decorator
        $this->app->bind(EmployeeRepositoryInterface::class, function ($app) {
            $repository = new EmployeeRepository();

            if (config('cache.default') === 'redis' || config('cache.default') === 'database') {
                return new CachedEmployeeRepository($repository);
            }

            return $repository;
        });

        // Bind Payroll repository with optional cache decorator
        $this->app->bind(PayrollRepositoryInterface::class, function ($app) {
            $repository = new PayrollRepository();

            if (config('cache.default') === 'redis' || config('cache.default') === 'database') {
                return new CachedPayrollRepository($repository);
            }

            return $repository;
        });

        // Bind Attendance repository (no cache decorator needed for real-time data)
        $this->app->bind(AttendanceRepositoryInterface::class, AttendanceRepository::class);
    }

    public function boot(): void
    {
        //
    }
}