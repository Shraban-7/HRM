<?php

namespace App\Providers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use App\Models\Payslip;
use App\Policies\DepartmentPolicy;
use App\Policies\EmployeePolicy;
use App\Policies\LeavePolicy;
use App\Policies\PayrollPolicy;
use App\Services\Attendance\AttendanceProcessor;
use App\Services\Attendance\LeaveCalculator;
use App\Services\Benefits\BenefitCostCalculator;
use App\Services\Benefits\BenefitDeductionService;
use App\Services\Benefits\CobraService;
use App\Services\Benefits\Providers\BenefitProviderRegistry;
use App\Services\Benefits\Providers\DentalProvider;
use App\Services\Benefits\Providers\HealthProvider;
use App\Services\Employee\EmployeeService;
use App\Services\Notification\EmailNotificationService;
use App\Services\Notification\InAppNotificationService;
use App\Services\Payroll\PayrollCalculator;
use App\Services\Payroll\PayslipGenerator;
use App\Services\Payroll\TaxEngine;
use App\Services\Performance\PerformanceBonusCalculator;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Gate;
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
        $this->app->singleton(BenefitCostCalculator::class);
        $this->app->singleton(BenefitDeductionService::class);
        $this->app->singleton(CobraService::class);
        $this->app->singleton(PerformanceBonusCalculator::class);
        $this->app->singleton(BenefitProviderRegistry::class, function (Application $app) {
            $registry = new BenefitProviderRegistry;
            $registry->register($app->make(HealthProvider::class));
            $registry->register($app->make(DentalProvider::class));

            return $registry;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        $this->registerGateBypass();
    }

    protected function registerPolicies(): void
    {
        Gate::policy(Employee::class, EmployeePolicy::class);
        Gate::policy(LeaveRequest::class, LeavePolicy::class);
        Gate::policy(Payroll::class, PayrollPolicy::class);
        Gate::policy(Payslip::class, PayrollPolicy::class);
        Gate::policy(Department::class, DepartmentPolicy::class);
    }

    protected function registerGateBypass(): void
    {
        Gate::before(function ($user, $ability) {
            $user = $user ?? auth_effective_user();

            if ($user && auth_has_role('super_admin')) {
                return true;
            }

            return null;
        });
    }
}
