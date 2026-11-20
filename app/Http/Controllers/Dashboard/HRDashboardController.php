<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use Illuminate\View\View;

class HRDashboardController extends Controller
{
    public function index(): View
    {
        $totalEmployees = Employee::count();
        $activeEmployees = Employee::where('status', 'active')->count();
        $totalDepartments = Department::count();
        $pendingLeaves = LeaveRequest::where('status', 'pending')->count();
        $activePayrolls = Payroll::where('status', 'draft')->orWhere('status', 'running')->count();

        // Chart Data: Department Distribution
        $departments = Department::withCount('employees')->having('employees_count', '>', 0)->get();
        if ($departments->isEmpty()) {
            $departments = Department::withCount('employees')->take(5)->get();
        }
        $deptLabels = $departments->pluck('name')->toArray();
        $deptCounts = $departments->pluck('employees_count')->toArray();

        // Chart Data: Recent Payrolls
        $recentPayrolls = Payroll::latest()->take(6)->get()->reverse();
        $payrollLabels = $recentPayrolls->map(fn ($p) => \Carbon\Carbon::parse($p->period_start)->format('M Y'))->toArray();
        $payrollAmounts = $recentPayrolls->pluck('total_amount')->toArray();

        // Fallback demo data if empty
        if (empty($deptLabels)) {
            $deptLabels = ['Engineering', 'Product', 'Marketing', 'Sales', 'HR'];
            $deptCounts = [14, 8, 6, 12, 4];
        }
        if (empty($payrollLabels)) {
            $payrollLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
            $payrollAmounts = [42000, 43500, 45000, 44200, 48000, 52000];
        }

        $recentHires = Employee::with(['user', 'department'])->latest()->take(5)->get();

        return view('dashboard.hr', compact(
            'totalEmployees', 'activeEmployees', 'totalDepartments',
            'pendingLeaves', 'activePayrolls', 'deptLabels', 'deptCounts',
            'payrollLabels', 'payrollAmounts', 'recentHires'
        ));
    }
}
