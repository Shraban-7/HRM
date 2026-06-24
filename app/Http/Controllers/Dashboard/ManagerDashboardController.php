<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class ManagerDashboardController extends Controller
{
    public function index()
    {
        $teamCount = Employee::where('status', 'active')->count();
        $pendingLeaves = LeaveRequest::where('status', 'pending')->count();
        $recentHires = Employee::where('hire_date', '>=', now()->subMonth())->count();

        return view('dashboard.manager', compact('teamCount', 'pendingLeaves', 'recentHires'));
    }
}