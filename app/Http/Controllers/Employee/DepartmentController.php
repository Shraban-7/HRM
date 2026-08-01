<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::with(['parent', 'manager'])
            ->withCount(['children', 'employees'])
            ->paginate(15);
        $managers = Employee::with('user')->where('status', 'active')->get();

        return view('employee.departments', compact('departments', 'managers'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Department::class);

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:departments,name',
            'parent_id' => 'nullable|exists:departments,id',
            'manager_id' => 'nullable|exists:users,id',
        ]);

        Department::create($validated);

        return redirect()->route('departments.index')->with('success', 'Department created.');
    }

    public function update(Request $request, Department $department)
    {
        $this->authorize('update', $department);

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:departments,name,'.$department->id,
            'parent_id' => ['nullable', 'exists:departments,id', function ($attribute, $value, $fail) use ($department) {
                if ($value !== null && $this->wouldCreateCycle($department, $value)) {
                    $fail('A department cannot be assigned as its own parent or descendant.');
                }
            }],
            'manager_id' => 'nullable|exists:users,id',
        ]);

        $department->update($validated);

        return redirect()->route('departments.index')->with('success', 'Department updated.');
    }

    public function destroy(Department $department)
    {
        $this->authorize('delete', $department);

        // Detach children before deleting to avoid orphaned hierarchy.
        Department::where('parent_id', $department->id)->update(['parent_id' => null]);
        $department->delete();

        return redirect()->route('departments.index')->with('success', 'Department deleted.');
    }

    /**
     * Determine whether assigning $candidateParentId as parent of $department
     * would create a circular hierarchy.
     */
    protected function wouldCreateCycle(Department $department, int $candidateParentId): bool
    {
        if ((int) $candidateParentId === (int) $department->id) {
            return true;
        }

        $ancestors = [];
        $cursor = Department::find($candidateParentId);

        while ($cursor !== null) {
            if ((int) $cursor->id === (int) $department->id) {
                return true;
            }

            if (isset($ancestors[(int) $cursor->id])) {
                break;
            }

            $ancestors[(int) $cursor->id] = true;
            $cursor = $cursor->parent_id ? Department::find($cursor->parent_id) : null;
        }

        return false;
    }
}
