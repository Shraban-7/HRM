<?php

namespace App\Providers;

use App\Events\EmployeeHired;
use App\Events\EmployeeTerminated;
use App\Events\LeaveApproved;
use App\Events\PayrollProcessed;
use App\Events\PerformanceReviewSubmitted;
use App\Events\SalaryChanged;
use App\Listeners\LogAuditEvent;
use App\Listeners\NotifyPayrollTeam;
use App\Listeners\SendWelcomeEmail;
use App\Listeners\UpdateOrgChart;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        EmployeeHired::class => [
            SendWelcomeEmail::class,
            UpdateOrgChart::class,
            LogAuditEvent::class,
        ],
        EmployeeTerminated::class => [
            LogAuditEvent::class,
            UpdateOrgChart::class,
        ],
        SalaryChanged::class => [
            LogAuditEvent::class,
        ],
        PayrollProcessed::class => [
            NotifyPayrollTeam::class,
            LogAuditEvent::class,
        ],
        LeaveApproved::class => [
            LogAuditEvent::class,
        ],
        PerformanceReviewSubmitted::class => [
            LogAuditEvent::class,
        ],
    ];

    public function boot(): void
    {
        //
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}