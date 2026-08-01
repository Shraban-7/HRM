<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateReport;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected const REPORT_TYPES = [
        'employee-list' => 'Employee List',
        'payroll-summary' => 'Payroll Summary',
        'attendance-summary' => 'Attendance Summary',
        'leave-balance' => 'Leave Balance',
        'department-summary' => 'Department Summary',
        'recruitment-summary' => 'Recruitment Summary',
        'benefits-summary' => 'Benefits Summary',
    ];

    public function index()
    {
        return view('report.index', ['reportTypes' => self::REPORT_TYPES]);
    }

    public function generate(Request $request)
    {
        $request->validate([
            'type' => 'required|in:'.implode(',', array_keys(self::REPORT_TYPES)),
            'period_start' => 'nullable|date',
            'period_end' => 'nullable|date',
        ]);

        $params = [];
        if ($request->filled('period_start')) {
            $params['period_start'] = $request->input('period_start');
        }
        if ($request->filled('period_end')) {
            $params['period_end'] = $request->input('period_end');
        }

        $user = auth_effective_user();

        if (! $user) {
            return redirect()->route('reports.index')
                ->with('error', 'Authentication required to generate reports.');
        }

        GenerateReport::dispatch($request->input('type'), $params, $user->id);

        return redirect()->route('reports.index')
            ->with('success', 'Report generation started. The output will be saved under the reports directory.');
    }
}
