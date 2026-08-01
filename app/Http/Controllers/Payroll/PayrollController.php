<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index()
    {
        $payrolls = Payroll::with('payslips.employee.user')->orderBy('id', 'desc')->paginate(15);

        return view('payroll.index', compact('payrolls'));
    }

    public function create()
    {
        return view('payroll.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'period_start' => 'required|date',
            'period_end' => 'required|date|after:period_start',
        ]);

        $payroll = Payroll::create([
            'period_start' => $request->period_start,
            'period_end' => $request->period_end,
            'status' => 'draft',
            'total_amount' => 0,
        ]);

        return redirect()->route('payrolls.show', $payroll)
            ->with('success', 'Payroll period created.');
    }

    public function show(Payroll $payroll)
    {
        $this->authorize('view', $payroll);
        $payroll->load('payslips.employee.user');

        return view('payroll.show', compact('payroll'));
    }

    public function calculate(Payroll $payroll)
    {
        $this->authorize('run', $payroll);
        $employees = Employee::where('status', 'active')->get();
        $totalAmount = 0;

        foreach ($employees as $employee) {
            $gross = $employee->salary;
            $deductions = $gross * 0.1; // 10% default deduction
            $net = $gross - $deductions;
            $totalAmount += $net;

            $payroll->payslips()->create([
                'employee_id' => $employee->id,
                'gross' => $gross,
                'deductions' => $deductions,
                'net' => $net,
                'issued_at' => now(),
            ]);
        }

        $payroll->update(['total_amount' => $totalAmount, 'status' => 'completed']);

        return redirect()->route('payrolls.show', $payroll)
            ->with('success', 'Payroll calculated successfully.');
    }
}
