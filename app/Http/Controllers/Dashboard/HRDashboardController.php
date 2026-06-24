<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Payroll;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class HRDashboardController extends Controller
{
    public function index()
    {
        $totalEmployees = Employee::count();
        $activeEmployees = Employee::where('status', 'active')->count();
        $totalDepartments = Department::count();
        $pendingLeaves = LeaveRequest::where('status', 'pending')->count();
        $activePayrolls = Payroll::where('status', 'running')->count();

        return view('dashboard.hr', compact(
            'totalEmployees', 'activeEmployees', 'totalDepartments',
            'pendingLeaves', 'activePayrolls'
        ));
    }
}