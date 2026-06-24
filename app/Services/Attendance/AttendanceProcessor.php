<?php

namespace App\Services\Attendance;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Support\Carbon;

class AttendanceProcessor
{
    /**
     * Clock in an employee.
     */
    public function clockIn(Employee $employee): Attendance
    {
        $today = Carbon::today();

        // Check if already clocked in today
        $existing = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        if ($existing && $existing->clock_in !== null) {
            throw new \RuntimeException('Employee is already clocked in for today.');
        }

        return Attendance::create([
            'employee_id' => $employee->id,
            'date' => $today,
            'clock_in' => now(),
            'status' => 'present',
        ]);
    }

    /**
     * Clock out an employee.
     */
    public function clockOut(Employee $employee): Attendance
    {
        $today = Carbon::today();

        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance) {
            throw new \RuntimeException('No clock-in record found for today.');
        }

        if ($attendance->clock_out !== null) {
            throw new \RuntimeException('Employee is already clocked out for today.');
        }

        $attendance->update([
            'clock_out' => now(),
            'status' => $this->determineStatus($attendance->clock_in, now()),
        ]);

        return $attendance->fresh();
    }

    /**
     * Get attendance records for an employee for a given period.
     */
    public function getAttendanceForPeriod(Employee $employee, $start, $end): \Illuminate\Database\Eloquent\Collection
    {
        $start = $start instanceof Carbon ? $start : Carbon::parse($start);
        $end = $end instanceof Carbon ? $end : Carbon::parse($end);

        return Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$start, $end])
            ->orderBy('date', 'desc')
            ->get();
    }

    /**
     * Calculate overtime for an employee on a given date.
     */
    public function calculateOvertime(Employee $employee, Carbon $date): float
    {
        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $date)
            ->first();

        if (!$attendance || !$attendance->clock_in || !$attendance->clock_out) {
            return 0.0;
        }

        $clockIn = Carbon::parse($attendance->clock_in);
        $clockOut = Carbon::parse($attendance->clock_out);
        $hoursWorked = $clockIn->diffInHours($clockOut);

        // Standard work hours (e.g., 8 hours)
        $standardHours = 8;

        return max(0, $hoursWorked - $standardHours);
    }

    /**
     * Get monthly attendance summary for an employee.
     */
    public function getMonthlyAttendance(int $employeeId, int $year, int $month): \Illuminate\Database\Eloquent\Collection
    {
        return Attendance::where('employee_id', $employeeId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date')
            ->get();
    }

    /**
     * Determine attendance status based on clock-in/out times.
     */
    protected function determineStatus($clockIn, $clockOut): string
    {
        $clockInTime = Carbon::parse($clockIn);
        $clockOutTime = Carbon::parse($clockOut);

        // Late if clocked in after 9:00 AM
        $lateThreshold = Carbon::parse($clockIn)->setHour(9)->setMinute(0)->setSecond(0);

        if ($clockInTime->gt($lateThreshold)) {
            return 'late';
        }

        $hoursWorked = $clockInTime->diffInHours($clockOutTime);

        if ($hoursWorked < 4) {
            return 'half-day';
        }

        return 'present';
    }
}