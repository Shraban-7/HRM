<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Services\Employee\OrgChartService;

class OrgChartController extends Controller
{
    public function index(OrgChartService $orgChartService)
    {
        $chart = $orgChartService->buildChart();
        return view('employee.org-chart', compact('chart'));
    }
}