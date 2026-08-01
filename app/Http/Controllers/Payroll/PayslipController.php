<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Payslip;
use App\Services\Payroll\PayslipGenerator;

class PayslipController extends Controller
{
    protected PayslipGenerator $payslipGenerator;

    public function __construct(PayslipGenerator $payslipGenerator)
    {
        $this->payslipGenerator = $payslipGenerator;
    }

    public function index()
    {
        $payslips = Payslip::with(['employee.user', 'payroll'])->paginate(15);

        return view('payroll.payslips', compact('payslips'));
    }

    public function show(Payslip $payslip)
    {
        $this->authorize('downloadPayslip', $payslip);
        $payslip->load(['employee.user', 'payroll']);

        return view('payroll.payslip-show', compact('payslip'));
    }

    public function download(Payslip $payslip)
    {
        $this->authorize('downloadPayslip', $payslip);

        return $this->payslipGenerator->downloadPayslip($payslip);
    }
}
