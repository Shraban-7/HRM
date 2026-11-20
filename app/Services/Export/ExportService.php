<?php

namespace App\Services\Export;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportService
{
    public function exportEmployeesCsv(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="employees_export_'.date('Y-m-d').'.csv"',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Employee ID', 'Username', 'Email', 'Department', 'Designation', 'Hire Date', 'Salary', 'Status']);

            Employee::with(['user', 'department'])->chunk(200, function ($employees) use ($handle) {
                foreach ($employees as $emp) {
                    fputcsv($handle, [
                        $emp->employee_id,
                        $emp->user->username ?? '',
                        $emp->user->email ?? '',
                        $emp->department->name ?? 'Unassigned',
                        $emp->designation,
                        $emp->hire_date,
                        $emp->salary,
                        $emp->status,
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }

    public function exportPayrollCsv(int $payrollId): StreamedResponse
    {
        $payroll = Payroll::with(['payslips.employee.user'])->findOrFail($payrollId);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="payroll_'.$payroll->id.'_summary.csv"',
        ];

        return response()->stream(function () use ($payroll) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Payslip ID', 'Employee ID', 'Employee Name', 'Gross Pay', 'Total Deductions', 'Net Salary', 'Issued Date']);

            foreach ($payroll->payslips as $slip) {
                fputcsv($handle, [
                    $slip->id,
                    $slip->employee->employee_id ?? 'N/A',
                    $slip->employee->user->username ?? 'Unknown',
                    $slip->gross,
                    $slip->deductions,
                    $slip->net,
                    $slip->issued_at,
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    public function exportAttendanceCsv(?string $month = null): StreamedResponse
    {
        $month = $month ?: date('Y-m');
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="attendance_'.$month.'.csv"',
        ];

        return response()->stream(function () use ($month) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'Employee ID', 'Employee Name', 'Clock In', 'Clock Out', 'Status']);

            Attendance::with('employee.user')
                ->where('date', 'like', "{$month}%")
                ->chunk(200, function ($records) use ($handle) {
                    foreach ($records as $att) {
                        fputcsv($handle, [
                            $att->date,
                            $att->employee->employee_id ?? '',
                            $att->employee->user->username ?? '',
                            $att->clock_in,
                            $att->clock_out,
                            $att->status,
                        ]);
                    }
                });

            fclose($handle);
        }, 200, $headers);
    }

    public function importEmployeesCsv(UploadedFile $file): array
    {
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle); // Skip header row
        $imported = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                // Expected format: username, email, department_id, employee_id, hire_date, designation, salary
                if (count($row) < 7) {
                    continue;
                }

                [$username, $email, $deptId, $empId, $hireDate, $designation, $salary] = $row;

                if (User::where('email', $email)->exists() || Employee::where('employee_id', $empId)->exists()) {
                    $errors[] = "Duplicate user/employee record: {$email} ({$empId})";
                    continue;
                }

                $user = User::create([
                    'username' => trim($username),
                    'email' => trim($email),
                    'password' => Hash::make('password123'),
                    'status' => 'active',
                ]);

                Employee::create([
                    'user_id' => $user->id,
                    'department_id' => is_numeric($deptId) ? (int) $deptId : null,
                    'employee_id' => trim($empId),
                    'hire_date' => trim($hireDate) ?: date('Y-m-d'),
                    'designation' => trim($designation),
                    'salary' => (float) $salary,
                    'status' => 'active',
                ]);

                $imported++;
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $errors[] = $e->getMessage();
        } finally {
            fclose($handle);
        }

        return ['imported' => $imported, 'errors' => $errors];
    }
}
