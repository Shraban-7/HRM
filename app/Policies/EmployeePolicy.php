<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmployeePolicy
{
    use HandlesAuthorization;

    public function viewAny(?User $user): bool
    {
        return auth_has_permission('employees.view');
    }

    public function view(?User $user, Employee $employee): bool
    {
        $user = $user ?? auth_effective_user();

        if (! $user) {
            return false;
        }

        if (auth_has_permission('employees.view')) {
            return true;
        }

        return $employee->user_id === $user->id;
    }

    public function create(?User $user): bool
    {
        return auth_has_permission('employees.create');
    }

    public function update(?User $user, Employee $employee): bool
    {
        return auth_has_permission('employees.edit');
    }

    public function delete(?User $user, Employee $employee): bool
    {
        return auth_has_permission('employees.delete');
    }
}
