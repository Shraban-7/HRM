@extends('layouts.app')
@section('title', 'Rating Calibration')
@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h3 class="text-lg font-semibold">Rating Calibration</h3>
        <a href="{{ route('reviews.index') }}" class="text-gray-600 hover:text-gray-800">&larr; Back to Reviews</a>
    </div>

    <div class="bg-white p-6 rounded-lg shadow">
        <h4 class="font-semibold mb-4">Current Rating Distribution</h4>
        <div class="flex gap-6">
            @foreach([5, 4, 3, 2, 1] as $rating)
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $distribution[$rating] ?? 0 }}</div>
                    <div class="text-sm text-gray-500">{{ $rating }} star</div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow">
        <table class="w-full">
            <thead>
                <tr class="text-left text-sm text-gray-500 border-b">
                    <th class="pb-2">Employee</th>
                    <th class="pb-2">Reviewer</th>
                    <th class="pb-2">Date</th>
                    <th class="pb-2">Current Rating</th>
                    <th class="pb-2">Recalibrate</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $review)
                    <tr class="border-b">
                        <td class="py-2">{{ $review->employee->user->username ?? 'N/A' }}</td>
                        <td class="py-2">{{ $review->reviewer->username ?? 'N/A' }}</td>
                        <td class="py-2">{{ $review->review_date }}</td>
                        <td class="py-2">{{ $review->rating ? $review->rating.'/5' : 'Unrated' }}</td>
                        <td class="py-2">
                            <form method="POST" action="{{ route('performance.calibration.update', $review) }}" class="flex gap-2">
                                @csrf
                                <select name="rating" class="border border-gray-300 rounded px-2 py-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}" @selected($review->rating == $i)>{{ $i }}</option>
                                    @endfor
                                </select>
                                <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">Apply</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-4 text-center text-gray-500">No reviews to calibrate.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">{{ $reviews->links() }}</div>
    </div>
</div>
@endsection
