<?php

namespace App\Http\Controllers\Recruitment;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\JobPost;
use App\Services\Employee\EmployeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApplicantController extends Controller
{
    protected EmployeeService $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    public function index()
    {
        $applicants = Applicant::with('jobPost')->orderBy('created_at', 'desc')->paginate(15);

        return view('recruitment.applicants', compact('applicants'));
    }

    public function create()
    {
        $jobs = JobPost::where('status', 'published')->get();

        return view('recruitment.applicant-create', compact('jobs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'job_post_id' => 'required|exists:job_posts,id',
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'nullable|string',
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        $data = $request->except('resume');
        if ($request->hasFile('resume')) {
            $data['resume_path'] = $request->file('resume')->store('resumes', 'public');
        }

        Applicant::create($data);

        return redirect()->route('applicants.index')->with('success', 'Application submitted.');
    }

    public function show(Applicant $applicant)
    {
        $applicant->load(['jobPost', 'interview']);

        return view('recruitment.applicant-show', compact('applicant'));
    }

    public function hire(Request $request, Applicant $applicant)
    {
        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'employee_id' => 'required|string|unique:employees,employee_id',
            'hire_date' => 'required|date',
            'designation' => 'required|string|max:255',
            'salary' => 'required|numeric|min:0|max:99999999.99',
        ]);

        $name = trim($applicant->name);
        $parts = preg_split('/\s+/', $name);

        $username = Str::slug($parts[0] ?? 'newhire').'_'.Str::lower(Str::random(4));

        $employee = $this->employeeService->createEmployee(
            ['email' => $applicant->email, 'username' => $username, 'status' => 'active'],
            [
                'department_id' => $validated['department_id'],
                'employee_id' => $validated['employee_id'],
                'hire_date' => $validated['hire_date'],
                'designation' => $validated['designation'],
                'salary' => $validated['salary'],
                'status' => 'active',
                'first_name' => $parts[0] ?? null,
                'last_name' => $parts[1] ?? null,
            ]
        );

        $applicant->update(['status' => 'hired']);

        return redirect()->route('applicants.show', $applicant)
            ->with('success', "Applicant hired as employee (ID: {$employee->employee_id}).");
    }
}
