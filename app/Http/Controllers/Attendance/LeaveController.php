<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Services\Notification\EmailNotificationService;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    protected EmailNotificationService $emailService;

    public function __construct(EmailNotificationService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function index()
    {
        $leaves = LeaveRequest::with(['employee.user', 'approver'])->orderBy('created_at', 'desc')->paginate(15);

        return view('attendance.leave-requests', compact('leaves'));
    }

    public function create()
    {
        return view('attendance.leave-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:annual,sick,other',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $employee = $this->authenticatedEmployee();

        if (! $employee) {
            return redirect()->route('leave.index')
                ->with('error', 'No employee profile is linked to your account.');
        }

        LeaveRequest::create([
            'employee_id' => $employee->id,
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => 'pending',
        ]);

        return redirect()->route('leave.index')->with('success', 'Leave request submitted.');
    }

    public function approve(LeaveRequest $leave)
    {
        $this->authorize('approve', $leave);
        $user = $this->authenticatedUser();
        $leave->update(['status' => 'approved', 'approved_by' => $user?->id, 'approved_at' => now()]);

        if ($leave->employee?->user) {
            $this->emailService->sendLeaveApprovalEmail($leave);
        }

        return redirect()->route('leave.index')->with('success', 'Leave approved.');
    }

    public function reject(LeaveRequest $leave)
    {
        $this->authorize('reject', $leave);
        $user = $this->authenticatedUser();
        $leave->update(['status' => 'rejected', 'approved_by' => $user?->id, 'approved_at' => now()]);

        if ($leave->employee?->user) {
            $this->emailService->sendLeaveApprovalEmail($leave);
        }

        return redirect()->route('leave.index')->with('success', 'Leave rejected.');
    }
}
