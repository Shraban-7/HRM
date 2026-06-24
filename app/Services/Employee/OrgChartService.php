<?php

namespace App\Services\Employee;

use App\Models\Department;

class OrgChartService
{
    public function buildChart(): array
    {
        $departments = Department::with(['manager', 'children', 'employees.user'])->get();
        return $this->buildTree($departments, null);
    }

    private function buildTree($departments, $parentId): array
    {
        $branch = [];
        foreach ($departments as $dept) {
            if ($dept->parent_id === $parentId) {
                $children = $this->buildTree($departments, $dept->id);
                $node = [
                    'id' => $dept->id,
                    'name' => $dept->name,
                    'manager' => $dept->manager ? $dept->manager->username : 'N/A',
                    'employee_count' => $dept->employees->count(),
                    'children' => $children,
                ];
                $branch[] = $node;
            }
        }
        return $branch;
    }
}