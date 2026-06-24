<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Payslip;
use App\Models\Payroll;

class PayslipController extends Controller
{
    public function index()
    {
        $payslips = Payslip::with(['employee.user', 'payroll'])->paginate(15);
        return view('payroll.payslips', compact('payslips'));
    }

    public function show(Payslip $payslip)
    {
        $payslip->load(['employee.user', 'payroll']);
        return view('payroll.payslip-show', compact('payslip'));
    }

    public function download(Payslip $payslip)
    {
        // PDF generation placeholder
        $payslip->load(['employee.user', 'payroll']);
        return view('payroll.payslip-pdf', compact('payslip'));
    }
}