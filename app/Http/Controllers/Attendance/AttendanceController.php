<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        $records = Attendance::with('employee.user')->orderBy('date', 'desc')->paginate(20);

        return view('attendance.index', compact('records'));
    }

    public function clockIn(Request $request)
    {
        $employee = $this->authenticatedEmployee();

        if (! $employee) {
            return redirect()->route('attendance.index')
                ->with('error', 'No employee profile is linked to your account.');
        }

        $today = now()->toDateString();

        $attendance = Attendance::firstOrCreate(
            ['employee_id' => $employee->id, 'date' => $today],
            ['clock_in' => now(), 'status' => 'present']
        );

        if (! $attendance->clock_in) {
            $attendance->update(['clock_in' => now(), 'status' => 'present']);
        }

        return redirect()->route('attendance.index')->with('success', 'Clocked in successfully.');
    }

    public function clockOut(Request $request)
    {
        $employee = $this->authenticatedEmployee();

        if (! $employee) {
            return redirect()->route('attendance.index')
                ->with('error', 'No employee profile is linked to your account.');
        }

        $today = now()->toDateString();

        $attendance = Attendance::where('employee_id', $employee->id)
            ->where('date', $today)
            ->firstOrFail();

        $attendance->update(['clock_out' => now()]);

        return redirect()->route('attendance.index')->with('success', 'Clocked out successfully.');
    }

    public function calendar()
    {
        $records = Attendance::with('employee.user')->whereMonth('date', now()->month)->get();

        return view('attendance.calendar', compact('records'));
    }
}
