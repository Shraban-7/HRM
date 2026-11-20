<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeOnboardingProgress;
use App\Models\OnboardingChecklist;
use App\Models\OnboardingTask;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function index(Request $request): View
    {
        $checklists = OnboardingChecklist::with('tasks', 'department')->get();
        $employees = Employee::with(['user', 'department', 'onboardingProgress.task'])->latest()->paginate(15);

        return view('employee.onboarding.index', compact('checklists', 'employees'));
    }

    public function storeChecklist(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'department_id' => 'nullable|exists:departments,id',
            'description' => 'nullable|string',
        ]);

        OnboardingChecklist::create($validated);

        return redirect()->route('onboarding.index')->with('success', 'Onboarding checklist template created.');
    }

    public function storeTask(Request $request, OnboardingChecklist $checklist): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'assigned_role' => 'required|string',
            'is_required' => 'nullable|boolean',
        ]);

        $validated['checklist_id'] = $checklist->id;
        $validated['is_required'] = $request->has('is_required');

        OnboardingTask::create($validated);

        return redirect()->route('onboarding.index')->with('success', 'Onboarding task added.');
    }

    public function toggleTask(Request $request, EmployeeOnboardingProgress $progress): RedirectResponse
    {
        $progress->update([
            'is_completed' => ! $progress->is_completed,
            'completed_at' => ! $progress->is_completed ? now() : null,
            'notes' => $request->input('notes', $progress->notes),
        ]);

        return redirect()->back()->with('success', 'Task progress updated.');
    }
}
