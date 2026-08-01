<?php

namespace App\Policies;

use App\Models\Department;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DepartmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(?User $user): bool
    {
        return auth_has_permission('departments.view');
    }

    public function view(?User $user, Department $department): bool
    {
        return auth_has_permission('departments.view');
    }

    public function create(?User $user): bool
    {
        return auth_has_permission('departments.create');
    }

    public function update(?User $user, Department $department): bool
    {
        return auth_has_permission('departments.edit');
    }

    public function delete(?User $user, Department $department): bool
    {
        return auth_has_permission('departments.delete');
    }
}
