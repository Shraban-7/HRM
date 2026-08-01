<?php

namespace App\Http\Controllers\Performance;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index()
    {
        $feedback = Feedback::with(['fromUser', 'toUser'])->orderBy('created_at', 'desc')->paginate(15);

        return view('performance.feedback', compact('feedback'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'to_user_id' => 'required|exists:users,id',
            'message' => 'required|string',
            'category' => 'required|in:general,performance,teamwork',
        ]);

        $user = $this->authenticatedUser();
        Feedback::create($request->all() + ['from_user_id' => $user?->id]);

        return redirect()->route('feedback.index')->with('success', 'Feedback submitted.');
    }
}
