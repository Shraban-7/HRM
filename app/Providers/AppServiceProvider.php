<?php

namespace App\Providers;

use App\Services\Attendance\AttendanceProcessor;
use App\Services\Attendance\LeaveCalculator;
use App\Services\Employee\EmployeeService;
use App\Services\Notification\EmailNotificationService;
use App\Services\Notification\InAppNotificationService;
use App\Services\Payroll\PayrollCalculator;
use App\Services\Payroll\PayslipGenerator;
use App\Services\Payroll\TaxEngine;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register services
        $this->app->singleton(EmployeeService::class);
        $this->app->singleton(TaxEngine::class);
        $this->app->singleton(PayrollCalculator::class);
        $this->app->singleton(PayslipGenerator::class);
        $this->app->singleton(AttendanceProcessor::class);
        $this->app->singleton(LeaveCalculator::class);
        $this->app->singleton(EmailNotificationService::class);
        $this->app->singleton(InAppNotificationService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
