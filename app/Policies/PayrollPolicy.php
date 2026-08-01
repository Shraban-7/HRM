<?php

namespace App\Policies;

use App\Models\Payroll;
use App\Models\Payslip;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PayrollPolicy
{
    use HandlesAuthorization;

    public function view(?User $user, Payroll $payroll): bool
    {
        return auth_has_permission('payroll.view');
    }

    public function run(?User $user, Payroll $payroll): bool
    {
        return auth_has_permission('payroll.run_payroll');
    }

    public function downloadPayslip(?User $user, Payslip $payslip): bool
    {
        $user = $user ?? auth_effective_user();

        if (! $user) {
            return false;
        }

        if (auth_has_permission('payroll.view_payslips')) {
            return true;
        }

        return $payslip->employee?->user_id === $user->id;
    }
}
