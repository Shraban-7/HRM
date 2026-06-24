<?php

namespace App\Http\Controllers\Recruitment;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\JobPost;
use Illuminate\Http\Request;

class ApplicantController extends Controller
{
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
}