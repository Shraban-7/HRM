<?php

namespace Tests\Unit\Services;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\User;
use App\Services\Attendance\AttendanceProcessor;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AttendanceProcessorTest extends TestCase
{
    protected AttendanceProcessor $processor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->processor = new AttendanceProcessor;
    }

    protected function makeEmployee(): Employee
    {
        $user = User::factory()->create();

        return Employee::factory()->create(['user_id' => $user->id]);
    }

    public function test_clock_in_creates_attendance_record(): void
    {
        $employee = $this->makeEmployee();

        $attendance = $this->processor->clockIn($employee);

        $this->assertEquals('present', $attendance->status);
        $this->assertEquals($employee->id, $attendance->employee_id);
        $this->assertDatabaseHas('attendances', [
            'employee_id' => $employee->id,
            'date' => Carbon::today()->toDateString(),
        ]);
    }

    public function test_clock_in_twice_throws_exception(): void
    {
        $employee = $this->makeEmployee();
        $this->processor->clockIn($employee);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('already clocked in');

        $this->processor->clockIn($employee);
    }

    public function test_clock_out_updates_record(): void
    {
        $employee = $this->makeEmployee();
        $this->processor->clockIn($employee);

        $attendance = $this->processor->clockOut($employee);

        $this->assertNotNull($attendance->clock_out);
    }

    public function test_clock_out_without_clock_in_throws(): void
    {
        $employee = $this->makeEmployee();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No clock-in');

        $this->processor->clockOut($employee);
    }

    public function test_calculate_overtime_counts_hours_beyond_standard(): void
    {
        $employee = $this->makeEmployee();

        Attendance::create([
            'employee_id' => $employee->id,
            'date' => Carbon::today()->toDateString(),
            'clock_in' => '08:00:00',
            'clock_out' => '20:00:00',
        ]);

        $overtime = $this->processor->calculateOvertime($employee, Carbon::today());

        $this->assertEquals(4.0, $overtime);
    }

    public function test_calculate_overtime_returns_zero_without_record(): void
    {
        $employee = $this->makeEmployee();

        $this->assertEquals(0.0, $this->processor->calculateOvertime($employee, Carbon::today()));
    }

    public function test_get_attendance_for_period_filters_by_dates(): void
    {
        $employee = $this->makeEmployee();

        Attendance::create([
            'employee_id' => $employee->id,
            'date' => Carbon::today()->subDays(5)->toDateString(),
            'clock_in' => '08:00:00',
            'clock_out' => '17:00:00',
        ]);
        Attendance::create([
            'employee_id' => $employee->id,
            'date' => Carbon::today()->subDays(30)->toDateString(),
            'clock_in' => '08:00:00',
            'clock_out' => '17:00:00',
        ]);

        $records = $this->processor->getAttendanceForPeriod(
            $employee,
            Carbon::today()->subDays(10),
            Carbon::today()
        );

        $this->assertCount(1, $records);
    }
}
