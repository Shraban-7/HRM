<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BiometricSyncController extends Controller
{
    public function sync(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'logs' => 'required|array',
            'logs.*.employee_code' => 'required|string',
            'logs.*.timestamp' => 'required|date',
            'logs.*.type' => 'required|in:in,out',
        ]);

        $processed = 0;
        $failed = 0;

        foreach ($validated['logs'] as $log) {
            $employee = Employee::where('employee_id', $log['employee_code'])->first();
            if (! $employee) {
                $failed++;
                continue;
            }

            $date = date('Y-m-d', strtotime($log['timestamp']));
            $time = date('H:i:s', strtotime($log['timestamp']));

            $attendance = Attendance::firstOrCreate(
                ['employee_id' => $employee->id, 'date' => $date],
                ['status' => 'present']
            );

            if ($log['type'] === 'in' && ! $attendance->clock_in) {
                $attendance->update(['clock_in' => $time]);
            } elseif ($log['type'] === 'out') {
                $attendance->update(['clock_out' => $time]);
            }

            $processed++;
        }

        return response()->json([
            'success' => true,
            'message' => "Biometric hardware batch processed: {$processed} records ingested, {$failed} unmapped.",
            'processed' => $processed,
            'failed' => $failed,
        ]);
    }
}
