<?php

namespace App\Repositories\Contracts;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

interface AttendanceRepositoryInterface
{
    public function findForEmployee(int $employeeId, array $filters = []): Collection;
    public function findByDate(int $employeeId, Carbon $date): ?Attendance;
    public function create(array $data): Attendance;
    public function update(Attendance $attendance, array $data): Attendance;
    public function getMonthlyAttendance(int $employeeId, int $year, int $month): Collection;
}