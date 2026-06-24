<?php

namespace App\Http\Controllers\Recruitment;

use App\Http\Controllers\Controller;
use App\Models\JobPost;
use Illuminate\Http\Request;

class JobPostController extends Controller
{
    public function index()
    {
        $jobs = JobPost::orderBy('created_at', 'desc')->paginate(15);
        return view('recruitment.jobs', compact('jobs'));
    }

    public function create()
    {
        return view('recruitment.job-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'closing_date' => 'nullable|date',
        ]);

        JobPost::create($request->all() + ['status' => 'draft']);
        return redirect()->route('jobs.index')->with('success', 'Job post created.');
    }

    public function show(JobPost $job)
    {
        $job->load('applicants.interview');
        return view('recruitment.job-show', compact('job'));
    }

    public function update(Request $request, JobPost $job)
    {
        $job->update($request->validate(['title' => 'required|string', 'description' => 'required|string', 'status' => 'required|in:draft,published,closed']));
        return redirect()->route('jobs.index')->with('success', 'Job post updated.');
    }

    public function destroy(JobPost $job)
    {
        $job->delete();
        return redirect()->route('jobs.index')->with('success', 'Job post deleted.');
    }
}