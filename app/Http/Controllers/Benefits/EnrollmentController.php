<?php

namespace App\Http\Controllers\Benefits;

use App\Http\Controllers\Controller;
use App\Models\BenefitEnrollment;
use App\Models\Benefit;
use App\Models\Employee;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function index()
    {
        $enrollments = BenefitEnrollment::with(['employee.user', 'benefit'])->paginate(15);
        return view('benefits.enroll', compact('enrollments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'benefit_id' => 'required|exists:benefits,id',
            'enrolled_at' => 'required|date',
            'expires_at' => 'nullable|date|after:enrolled_at',
        ]);

        $employee = Employee::where('user_id', auth()->id())->firstOrFail();

        BenefitEnrollment::create($request->all() + ['employee_id' => $employee->id, 'status' => 'active']);
        return redirect()->route('enrollments.index')->with('success', 'Enrolled successfully.');
    }

    public function destroy(BenefitEnrollment $enrollment)
    {
        $enrollment->update(['status' => 'cancelled']);
        return redirect()->route('enrollments.index')->with('success', 'Enrollment cancelled.');
    }
}