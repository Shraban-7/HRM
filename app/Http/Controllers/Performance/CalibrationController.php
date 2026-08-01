<?php

namespace App\Http\Controllers\Performance;

use App\Http\Controllers\Controller;
use App\Models\PerformanceReview;
use Illuminate\Http\Request;

class CalibrationController extends Controller
{
    public function index()
    {
        $reviews = PerformanceReview::with(['employee.user', 'reviewer'])
            ->orderByRaw('COALESCE(rating, 0) DESC')
            ->paginate(25);

        $distribution = PerformanceReview::whereNotNull('rating')
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating');

        return view('performance.calibration', compact('reviews', 'distribution'));
    }

    public function update(Request $request, PerformanceReview $review)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'status' => 'required|in:pending,completed',
        ]);

        $review->update($validated);

        return redirect()->route('performance.calibration.index')
            ->with('success', "Rating recalibrated to {$validated['rating']}.");
    }
}
