<?php

use App\Http\Controllers\Api\V1\AttendanceApiController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BiometricSyncController;
use App\Http\Controllers\Api\V1\EmployeeApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public Token Issue Endpoint
    Route::post('auth/token', [AuthController::class, 'token'])->middleware('throttle:10,1');

    // Authenticated API Routes
    Route::middleware(['api.token'])->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('auth/revoke', [AuthController::class, 'revoke']);

        Route::get('employees', [EmployeeApiController::class, 'index']);
        Route::get('employees/{employee}', [EmployeeApiController::class, 'show']);

        Route::post('attendance/clock-in', [AttendanceApiController::class, 'clockIn']);
        Route::post('attendance/clock-out', [AttendanceApiController::class, 'clockOut']);
        Route::get('attendance/today', [AttendanceApiController::class, 'today']);

        // Biometric Hardware Device Integration Webhook
        Route::post('integrations/biometric-sync', [BiometricSyncController::class, 'sync']);
    });
});
