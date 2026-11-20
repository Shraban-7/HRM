<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateReport;
use App\Models\Payroll;
use App\Services\Export\ExportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        $payrolls = Payroll::latest()->take(10)->get();

        return view('report.index', [
            'reportTypes' => self::REPORT_TYPES,
            'payrolls' => $payrolls,
        ]);
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
            ->with('success', 'Report generation queued in background.');
    }

    public function exportEmployees(ExportService $exportService): StreamedResponse
    {
        return $exportService->exportEmployeesCsv();
    }

    public function exportPayroll(Request $request, ExportService $exportService): StreamedResponse
    {
        $payrollId = (int) $request->input('payroll_id', Payroll::latest()->value('id') ?? 1);

        return $exportService->exportPayrollCsv($payrollId);
    }

    public function exportAttendance(Request $request, ExportService $exportService): StreamedResponse
    {
        return $exportService->exportAttendanceCsv($request->input('month'));
    }

    public function importEmployees(Request $request, ExportService $exportService): RedirectResponse
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $result = $exportService->importEmployeesCsv($request->file('csv_file'));

        if (! empty($result['errors'])) {
            return redirect()->route('reports.index')
                ->with('error', "Import completed with {$result['imported']} records added. Some records failed: ".implode('; ', array_slice($result['errors'], 0, 3)));
        }

        return redirect()->route('reports.index')
            ->with('success', "Successfully imported {$result['imported']} employees via CSV batch.");
    }
}
