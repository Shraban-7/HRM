<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::with(['parent', 'manager', 'children'])->paginate(15);
        return view('employee.departments', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string', 'parent_id' => 'nullable|exists:departments,id']);
        Department::create($request->only(['name', 'parent_id']));
        return redirect()->route('departments.index')->with('success', 'Department created.');
    }

    public function update(Request $request, Department $department)
    {
        $department->update($request->validate(['name' => 'required|string', 'parent_id' => 'nullable|exists:departments,id']));
        return redirect()->route('departments.index')->with('success', 'Department updated.');
    }

    public function destroy(Department $department)
    {
        $department->delete();
        return redirect()->route('departments.index')->with('success', 'Department deleted.');
    }
}