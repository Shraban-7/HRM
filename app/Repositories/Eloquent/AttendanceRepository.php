<?php

namespace App\Repositories\Eloquent;

use App\Models\Attendance;
use App\Repositories\Contracts\AttendanceRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class AttendanceRepository implements AttendanceRepositoryInterface
{
    public function findForEmployee(int $employeeId, array $filters = []): Collection
    {
        $query = Attendance::where('employee_id', $employeeId);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['from'])) {
            $query->where('date', '>=', $filters['from']);
        }

        if (!empty($filters['to'])) {
            $query->where('date', '<=', $filters['to']);
        }

        return $query->orderBy('date', 'desc')->get();
    }

    public function findByDate(int $employeeId, Carbon $date): ?Attendance
    {
        return Attendance::where('employee_id', $employeeId)
            ->whereDate('date', $date)
            ->first();
    }

    public function create(array $data): Attendance
    {
        return Attendance::create($data);
    }

    public function update(Attendance $attendance, array $data): Attendance
    {
        $attendance->update($data);
        return $attendance->fresh();
    }

    public function getMonthlyAttendance(int $employeeId, int $year, int $month): Collection
    {
        return Attendance::where('employee_id', $employeeId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date')
            ->get();
    }
}