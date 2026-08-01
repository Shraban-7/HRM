<?php

namespace App\Http\Controllers\Recruitment;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\JobPost;
use Illuminate\Http\Request;

class CareerController extends Controller
{
    public function index()
    {
        $jobs = JobPost::where('status', 'published')
            ->where(function ($query) {
                $query->whereNull('closing_date')->orWhere('closing_date', '>=', now()->toDateString());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('recruitment.career-index', compact('jobs'));
    }

    public function show(JobPost $job)
    {
        abort_unless($job->status === 'published', 404);

        return view('recruitment.career-show', compact('job'));
    }

    public function apply(Request $request, JobPost $job)
    {
        abort_unless($job->status === 'published', 404);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'resume' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);

        $data = [
            'job_post_id' => $job->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'resume_path' => $request->file('resume')->store('resumes', 'public'),
            'status' => 'applied',
        ];

        Applicant::create($data);

        return redirect()->route('careers.show', $job)
            ->with('success', 'Application submitted successfully.');
    }
}
