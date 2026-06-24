<?php

namespace App\Http\Controllers\Performance;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use App\Models\Employee;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    public function index()
    {
        $goals = Goal::with('employee.user')->orderBy('created_at', 'desc')->paginate(15);
        return view('performance.goals', compact('goals'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'target_date' => 'nullable|date',
        ]);

        Goal::create($request->all());
        return redirect()->route('goals.index')->with('success', 'Goal created.');
    }

    public function update(Request $request, Goal $goal)
    {
        $goal->update($request->validate(['status' => 'required|in:pending,in_progress,completed,cancelled']));
        return redirect()->route('goals.index')->with('success', 'Goal updated.');
    }
}