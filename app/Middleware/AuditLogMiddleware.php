<?php

namespace App\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\AuditLog;

class AuditLogMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return $next($request);
        }

        $beforeData = null;
        if (in_array($request->method(), ['PUT', 'PATCH', 'DELETE'])) {
            $resourceId = $request->route()->parameter('id') ?? $request->route()->parameter('employee');
            if ($resourceId) {
                $resource = $this->getResourceName($request);
                $model = $this->getModelInstance($resource, $resourceId);
                if ($model) {
                    $beforeData = $model->toJson();
                }
            }
        }

        $response = $next($request);

        $afterData = null;
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH'])) {
            $afterData = json_encode($request->all());
        }

        AuditLog::create([
            'user_id' => Session::get('user_id'),
            'action' => strtolower($request->method()) . '_' . $this->getResourceName($request),
            'resource' => $this->getResourceName($request),
            'resource_id' => $request->route()->parameter('id') ?? $request->route()->parameter('employee'),
            'before_data' => $beforeData,
            'after_data' => $afterData,
            'ip_address' => $request->ip(),
            'created_at' => now(),
        ]);

        return $response;
    }

    private function getResourceName(Request $request): string
    {
        $segments = explode('/', $request->path());
        return $segments[0] ?? 'unknown';
    }

    private function getModelInstance(string $resource, $id)
    {
        $modelMap = [
            'employees' => \App\Models\Employee::class,
            'departments' => \App\Models\Department::class,
            'payrolls' => \App\Models\Payroll::class,
            'payslips' => \App\Models\Payslip::class,
            'attendance' => \App\Models\Attendance::class,
            'leave-requests' => \App\Models\LeaveRequest::class,
            'job-posts' => \App\Models\JobPost::class,
            'applicants' => \App\Models\Applicant::class,
            'interviews' => \App\Models\Interview::class,
            'performance-reviews' => \App\Models\PerformanceReview::class,
            'goals' => \App\Models\Goal::class,
            'benefits' => \App\Models\Benefit::class,
            'benefit-enrollments' => \App\Models\BenefitEnrollment::class,
        ];

        $class = $modelMap[$resource] ?? null;
        if ($class) {
            return $class::find($id);
        }
        return null;
    }
}