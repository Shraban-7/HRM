<?php

namespace App\Http\Controllers\Recruitment;

use App\Http\Controllers\Controller;
use App\Models\Interview;
use App\Models\Applicant;
use Illuminate\Http\Request;

class InterviewController extends Controller
{
    public function index()
    {
        $interviews = Interview::with(['applicant', 'jobPost'])->orderBy('scheduled_at', 'desc')->paginate(15);
        return view('recruitment.interviews', compact('interviews'));
    }

    public function create(Applicant $applicant)
    {
        return view('recruitment.interview-create', compact('applicant'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'applicant_id' => 'required|exists:applicants,id',
            'job_post_id' => 'required|exists:job_posts,id',
            'scheduled_at' => 'required|date',
        ]);

        Interview::create($request->all());
        return redirect()->route('interviews.index')->with('success', 'Interview scheduled.');
    }

    public function update(Request $request, Interview $interview)
    {
        $interview->update($request->validate(['status' => 'required|in:scheduled,completed,cancelled', 'notes' => 'nullable|string']));
        return redirect()->route('interviews.index')->with('success', 'Interview updated.');
    }
}