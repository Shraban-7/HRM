<?php

namespace App\Listeners;

use App\Events\EmployeeHired;
use App\Events\EmployeeTerminated;
use App\Events\SalaryChanged;
use App\Events\PayrollProcessed;
use App\Events\LeaveApproved;
use App\Events\PerformanceReviewSubmitted;
use App\Models\AuditLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Request;

class LogAuditEvent implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(
        EmployeeHired|EmployeeTerminated|SalaryChanged|PayrollProcessed|LeaveApproved|PerformanceReviewSubmitted $event
    ): void {
        $log = $this->buildAuditLog($event);

        if ($log) {
            AuditLog::create($log);
        }
    }

    protected function buildAuditLog($event): ?array
    {
        $log = [
            'user_id' => $this->getUserId($event),
            'ip_address' => Request::ip() ?? '127.0.0.1',
            'created_at' => now(),
        ];

        switch (true) {
            case $event instanceof EmployeeHired:
                $log['action'] = 'employee.hired';
                $log['resource'] = 'employee';
                $log['resource_id'] = (string)$event->employee->id;
                $log['after_data'] = json_encode($event->employee->toArray());
                break;

            case $event instanceof EmployeeTerminated:
                $log['action'] = 'employee.terminated';
                $log['resource'] = 'employee';
                $log['resource_id'] = (string)$event->employee->id;
                $log['before_data'] = json_encode($event->employee->toArray());
                break;

            case $event instanceof SalaryChanged:
                $log['action'] = 'employee.salary_changed';
                $log['resource'] = 'employee';
                $log['resource_id'] = (string)$event->employee->id;
                $log['before_data'] = json_encode(['salary' => $event->oldSalary]);
                $log['after_data'] = json_encode(['salary' => $event->newSalary]);
                break;

            case $event instanceof PayrollProcessed:
                $log['action'] = 'payroll.processed';
                $log['resource'] = 'payroll';
                $log['resource_id'] = (string)$event->payroll->id;
                $log['after_data'] = json_encode([
                    'total_amount' => $event->payroll->total_amount,
                    'status' => $event->payroll->status,
                ]);
                break;

            case $event instanceof LeaveApproved:
                $log['action'] = 'leave.approved';
                $log['resource'] = 'leave_request';
                $log['resource_id'] = (string)$event->leaveRequest->id;
                $log['after_data'] = json_encode([
                    'employee_id' => $event->leaveRequest->employee_id,
                    'status' => $event->leaveRequest->status,
                ]);
                break;

            case $event instanceof PerformanceReviewSubmitted:
                $log['action'] = 'performance.review_submitted';
                $log['resource'] = 'performance_review';
                $log['resource_id'] = (string)$event->review->id;
                $log['after_data'] = json_encode([
                    'employee_id' => $event->review->employee_id,
                    'rating' => $event->review->rating,
                ]);
                break;

            default:
                return null;
        }

        return $log;
    }

    protected function getUserId($event): ?int
    {
        // Try to get user from session
        if (session()->has('user_id')) {
            return (int)session('user_id');
        }

        // Try from event relationships
        if (isset($event->employee) && $event->employee->user_id) {
            return $event->employee->user_id;
        }

        if (isset($event->leaveRequest) && $event->leaveRequest->approved_by) {
            return $event->leaveRequest->approved_by;
        }

        return null;
    }
}