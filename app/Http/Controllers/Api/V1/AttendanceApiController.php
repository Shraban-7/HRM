<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Services\Attendance\AttendanceProcessor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceApiController extends Controller
{
    public function clockIn(Request $request, AttendanceProcessor $processor): JsonResponse
    {
        $user = $request->user();
        if (! $user->employee) {
            return response()->json(['success' => false, 'message' => 'Authenticated user is not linked to an employee profile.'], 404);
        }

        $attendance = $processor->clockIn($user->employee);

        return response()->json([
            'success' => true,
            'message' => 'Clock-in recorded successfully.',
            'data' => $attendance,
        ]);
    }

    public function clockOut(Request $request, AttendanceProcessor $processor): JsonResponse
    {
        $user = $request->user();
        if (! $user->employee) {
            return response()->json(['success' => false, 'message' => 'Authenticated user is not linked to an employee profile.'], 404);
        }

        $attendance = $processor->clockOut($user->employee);

        return response()->json([
            'success' => true,
            'message' => 'Clock-out recorded successfully.',
            'data' => $attendance,
        ]);
    }

    public function today(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user->employee) {
            return response()->json(['success' => false, 'message' => 'Employee profile not found.'], 404);
        }

        $attendance = Attendance::where('employee_id', $user->employee->id)
            ->where('date', date('Y-m-d'))
            ->first();

        return response()->json([
            'success' => true,
            'data' => $attendance,
        ]);
    }
}
