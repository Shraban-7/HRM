<?php

namespace App\Services\Payroll;

use App\Models\Payslip;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PayslipGenerator
{
    /**
     * Generate a PDF payslip for a single payslip record.
     */
    public function generatePayslip(Payslip $payslip): string
    {
        $payslip->load('employee.user', 'employee.department');

        $pdf = Pdf::loadView('pdf.payslip', [
            'payslip' => $payslip,
            'employee' => $payslip->employee,
            'payroll' => $payslip->payroll,
        ]);

        $filename = 'payslips/payslip_' . $payslip->employee->employee_id . '_' . $payslip->id . '.pdf';
        Storage::disk('public')->put($filename, $pdf->output());

        $payslip->update(['pdf_path' => $filename]);

        return $filename;
    }

    /**
     * Generate PDFs for all payslips in a payroll.
     */
    public function generateBatchPayslips(int $payrollId): array
    {
        $payslips = Payslip::where('payroll_id', $payrollId)
            ->with('employee.user', 'employee.department')
            ->get();

        $paths = [];

        foreach ($payslips as $payslip) {
            $paths[] = $this->generatePayslip($payslip);
        }

        return $paths;
    }

    /**
     * Generate a combined PDF for all payslips in a payroll.
     */
    public function generateBatchPayslipPdf(int $payrollId): string
    {
        $payslips = Payslip::where('payroll_id', $payrollId)
            ->with('employee.user', 'employee.department', 'payroll')
            ->get();

        $pdf = Pdf::loadView('pdf.payslips-batch', [
            'payslips' => $payslips,
            'payroll' => $payslips->first()?->payroll,
        ]);

        $filename = 'payslips/batch_payroll_' . $payrollId . '.pdf';
        Storage::disk('public')->put($filename, $pdf->output());

        return $filename;
    }

    /**
     * Stream a payslip PDF for download.
     */
    public function downloadPayslip(Payslip $payslip)
    {
        $payslip->load('employee.user', 'employee.department');

        $pdf = Pdf::loadView('pdf.payslip', [
            'payslip' => $payslip,
            'employee' => $payslip->employee,
            'payroll' => $payslip->payroll,
        ]);

        $filename = 'payslip_' . $payslip->employee->employee_id . '_' . $payslip->id . '.pdf';

        return $pdf->download($filename);
    }
}