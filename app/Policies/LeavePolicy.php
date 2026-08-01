<?php

namespace App\Policies;

use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LeavePolicy
{
    use HandlesAuthorization;

    public function view(?User $user, LeaveRequest $leave): bool
    {
        $user = $user ?? auth_effective_user();

        if (! $user) {
            return false;
        }

        if (auth_has_permission('leave.approve')) {
            return true;
        }

        return $leave->employee?->user_id === $user->id;
    }

    public function apply(?User $user): bool
    {
        return auth_has_permission('leave.apply');
    }

    public function approve(?User $user, LeaveRequest $leave): bool
    {
        $user = $user ?? auth_effective_user();

        if (! $user || ! auth_has_permission('leave.approve')) {
            return false;
        }

        if (auth_has_role('hr_admin') || auth_has_role('super_admin')) {
            return true;
        }

        if (auth_has_role('manager')) {
            return $leave->employee?->department_id === $user->employee?->department_id;
        }

        return false;
    }

    public function reject(?User $user, LeaveRequest $leave): bool
    {
        return $this->approve($user, $leave);
    }
}
