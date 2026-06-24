<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Auth\CustomAuthService;
use Illuminate\Http\Request;

class EmployeeDashboardController extends Controller
{
    public function index(CustomAuthService $auth)
    {
        $user = $auth->getCurrentUser();
        $employee = $user->employee;

        return view('dashboard.employee', compact('user', 'employee'));
    }
}