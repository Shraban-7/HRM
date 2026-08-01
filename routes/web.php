<?php

use App\Http\Controllers\Attendance\AttendanceController;
use App\Http\Controllers\Attendance\LeaveController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Benefits\BenefitController;
use App\Http\Controllers\Benefits\EnrollmentController;
use App\Http\Controllers\Dashboard\EmployeeDashboardController;
use App\Http\Controllers\Dashboard\HRDashboardController;
use App\Http\Controllers\Dashboard\ManagerDashboardController;
use App\Http\Controllers\Employee\DepartmentController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\Employee\OrgChartController;
use App\Http\Controllers\Notification\NotificationController;
use App\Http\Controllers\Payroll\PayrollController;
use App\Http\Controllers\Payroll\PayslipController;
use App\Http\Controllers\Payroll\TaxController;
use App\Http\Controllers\Performance\FeedbackController;
use App\Http\Controllers\Performance\GoalController;
use App\Http\Controllers\Performance\ReviewController;
use App\Http\Controllers\Recruitment\ApplicantController;
use App\Http\Controllers\Recruitment\InterviewController;
use App\Http\Controllers\Recruitment\JobPostController;
use App\Models\AuditLog;
use App\Services\Auth\CustomAuthService;
use Illuminate\Support\Facades\Route;

// Root redirect.
Route::get('/', function (CustomAuthService $authService) {
    return $authService->isAuthenticated()
        ? redirect()->route('dashboard')
        : redirect()->route('auth.login');
});

// Public Auth Routes
Route::name('auth.')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:6,1');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->middleware('throttle:6,1');
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])->name('forgot-password');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->middleware('throttle:6,1');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('reset-password');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->middleware('throttle:6,1');
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout')->middleware('custom.auth');
});

// Protected Routes
Route::middleware(['custom.auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        $roles = session('user_roles', []);
        if (in_array('hr_admin', $roles) || in_array('super_admin', $roles)) {
            return app(HRDashboardController::class)->index();
        }
        if (in_array('manager', $roles)) {
            return app(ManagerDashboardController::class)->index();
        }

        return app(EmployeeDashboardController::class)->index(app(CustomAuthService::class));
    })->name('dashboard');

    // Employee Management
    Route::resource('employees', EmployeeController::class)->except(['show'])->middleware('permission:employees.view');
    Route::get('employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
    Route::resource('departments', DepartmentController::class)->except(['create', 'edit'])->middleware('permission:departments.view');
    Route::get('org-chart', [OrgChartController::class, 'index'])->name('org-chart');

    // Payroll
    Route::prefix('payroll')->name('payrolls.')->group(function () {
        Route::get('/', [PayrollController::class, 'index'])->name('index');
        Route::get('/create', [PayrollController::class, 'create'])->name('create');
        Route::post('/', [PayrollController::class, 'store'])->name('store');
        Route::get('/{payroll}', [PayrollController::class, 'show'])->name('show');
        Route::post('/{payroll}/calculate', [PayrollController::class, 'calculate'])->name('calculate')->middleware('permission:payroll.run_payroll');
    });
    Route::get('payslips', [PayslipController::class, 'index'])->name('payslips.index');
    Route::get('payslips/{payslip}', [PayslipController::class, 'show'])->name('payslips.show');
    Route::get('payslips/{payslip}/download', [PayslipController::class, 'download'])->name('payslips.download');
    Route::get('tax', [TaxController::class, 'index'])->name('tax.index');

    // Attendance
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::post('/clock-in', [AttendanceController::class, 'clockIn'])->name('clock-in');
        Route::post('/clock-out', [AttendanceController::class, 'clockOut'])->name('clock-out');
        Route::get('/calendar', [AttendanceController::class, 'calendar'])->name('calendar');
    });
    Route::prefix('leave')->name('leave.')->group(function () {
        Route::get('/', [LeaveController::class, 'index'])->name('index');
        Route::get('/create', [LeaveController::class, 'create'])->name('create');
        Route::post('/', [LeaveController::class, 'store'])->name('store');
        Route::post('/{leave}/approve', [LeaveController::class, 'approve'])->name('approve')->middleware('permission:leave.approve');
        Route::post('/{leave}/reject', [LeaveController::class, 'reject'])->name('reject')->middleware('permission:leave.approve');
    });

    // Recruitment
    Route::resource('jobs', JobPostController::class)->except(['edit']);
    Route::resource('applicants', ApplicantController::class)->only(['index', 'create', 'store', 'show']);
    Route::resource('interviews', InterviewController::class)->only(['index', 'create', 'store', 'update']);

    // Performance
    Route::resource('reviews', ReviewController::class)->only(['index', 'create', 'store', 'show', 'update']);
    Route::resource('goals', GoalController::class)->only(['index', 'store', 'update']);
    Route::resource('feedback', FeedbackController::class)->only(['index', 'store']);

    // Benefits
    Route::resource('benefits', BenefitController::class)->only(['index', 'store', 'update']);
    Route::resource('enrollments', EnrollmentController::class)->only(['index', 'store', 'destroy']);

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
    });

    // Audit Logs
    Route::get('audit-logs', function () {
        $logs = AuditLog::with('user')->orderBy('created_at', 'desc')->paginate(50);

        return view('layouts.audit', compact('logs'));
    })->name('audit.index')->middleware('permission:audit.view');
});
