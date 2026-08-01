<?php

namespace App\Http\Controllers\Performance;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\PerformanceFormTemplate;
use App\Models\PerformanceReview;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = PerformanceReview::with(['employee.user', 'reviewer'])->orderBy('created_at', 'desc')->paginate(15);

        return view('performance.reviews', compact('reviews'));
    }

    public function create()
    {
        $employees = Employee::with('user')->where('status', 'active')->get();
        $templates = PerformanceFormTemplate::where('is_active', true)->get();

        return view('performance.review-create', compact('employees', 'templates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'review_date' => 'required|date',
            'rating' => 'nullable|integer|min:1|max:5',
            'notes' => 'nullable|string',
            'form_template_id' => 'nullable|exists:performance_form_templates,id',
        ]);

        $user = $this->authenticatedUser();
        PerformanceReview::create($request->all() + ['reviewer_id' => $user?->id, 'status' => 'pending']);

        return redirect()->route('reviews.index')->with('success', 'Review created.');
    }

    public function show(PerformanceReview $review)
    {
        $review->load(['employee.user', 'reviewer']);

        return view('performance.review-show', compact('review'));
    }

    public function update(Request $request, PerformanceReview $review)
    {
        $review->update($request->validate(['rating' => 'nullable|integer|min:1|max:5', 'notes' => 'nullable|string', 'status' => 'required|in:pending,completed']));

        return redirect()->route('reviews.index')->with('success', 'Review updated.');
    }

    public function analytics()
    {
        $reviews = PerformanceReview::with('employee.user')->whereNotNull('rating')->get();
        $average = round((float) $reviews->avg('rating'), 2);
        $distribution = $reviews->groupBy('rating')->map->count();
        $completed = PerformanceReview::where('status', 'completed')->count();
        $pending = PerformanceReview::where('status', 'pending')->count();
        $top = $reviews->sortByDesc('rating')->take(5);

        return view('performance.analytics', compact('average', 'distribution', 'completed', 'pending', 'top'));
    }
}
