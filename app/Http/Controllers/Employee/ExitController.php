<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\ExitClearance;
use App\Models\ExitRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ExitController extends Controller
{
    public function index(): View
    {
        $exitRequests = ExitRequest::with(['employee.user', 'employee.department', 'clearances.clearedByUser'])->latest()->paginate(15);
        $employees = Employee::with('user')->where('status', 'active')->get();

        return view('employee.exit.index', compact('exitRequests', 'employees'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'resignation_date' => 'required|date',
            'last_working_day' => 'required|date|after_or_equal:resignation_date',
            'reason' => 'nullable|string',
            'interview_notes' => 'nullable|string',
        ]);

        $exit = ExitRequest::create($validated);

        // Auto-create standard department clearances
        $departments = ['IT Asset Return', 'HR Documentation', 'Finance Settlement', 'Operations Handover'];
        foreach ($departments as $dept) {
            ExitClearance::create([
                'exit_request_id' => $exit->id,
                'department' => $dept,
                'status' => 'pending',
            ]);
        }

        return redirect()->route('exit.index')->with('success', 'Employee resignation and clearance request logged.');
    }

    public function clearDepartment(Request $request, ExitClearance $clearance): RedirectResponse
    {
        $clearance->update([
            'status' => 'cleared',
            'cleared_by' => Auth::id() ?? session('user_id'),
            'cleared_at' => now(),
            'remarks' => $request->input('remarks'),
        ]);

        // If all clearances cleared, complete exit request and terminate employee
        $allCleared = $clearance->exitRequest->clearances()->where('status', '!=', 'cleared')->count() === 0;
        if ($allCleared) {
            $clearance->exitRequest->update(['status' => 'completed']);
            $clearance->exitRequest->employee->update(['status' => 'terminated']);
        }

        return redirect()->back()->with('success', "Clearance recorded for {$clearance->department}.");
    }
}
