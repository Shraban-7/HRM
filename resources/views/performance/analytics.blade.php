@extends('layouts.app')
@section('title', 'Performance Analytics')
@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h3 class="text-lg font-semibold">Performance Analytics</h3>
        <a href="{{ route('reviews.index') }}" class="text-gray-600 hover:text-gray-800">&larr; Back to Reviews</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <p class="text-sm text-gray-500">Average Rating</p>
            <p class="text-3xl font-bold mt-2">{{ $average }}</p>
            <p class="text-sm text-gray-500 mt-1">across {{ $distribution->sum() }} rated reviews</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <p class="text-sm text-gray-500">Completed Reviews</p>
            <p class="text-3xl font-bold mt-2 text-green-600">{{ $completed }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <p class="text-sm text-gray-500">Pending Reviews</p>
            <p class="text-3xl font-bold mt-2 text-yellow-600">{{ $pending }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <h4 class="font-semibold mb-4">Rating Distribution</h4>
            @if($distribution->isNotEmpty())
                <div class="space-y-3">
                    @foreach([5, 4, 3, 2, 1] as $rating)
                        <div class="flex items-center">
                            <span class="w-12 text-sm text-gray-500">{{ $rating }} star</span>
                            <div class="flex-1 bg-gray-100 rounded h-5 ml-2">
                                <div class="bg-blue-600 h-5 rounded" style="width: {{ $distribution->has($rating) ? $distribution[$rating] / $distribution->sum() * 100 : 0 }}%"></div>
                            </div>
                            <span class="w-12 text-right text-sm text-gray-600 ml-2">{{ $distribution[$rating] ?? 0 }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">No rated reviews yet.</p>
            @endif
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <h4 class="font-semibold mb-4">Top Rated Employees</h4>
            @if($top->isNotEmpty())
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-sm text-gray-500 border-b">
                            <th class="pb-2">Employee</th>
                            <th class="pb-2">Rating</th>
                            <th class="pb-2">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($top as $review)
                            <tr class="border-b">
                                <td class="py-2">{{ $review->employee->user->username ?? 'N/A' }}</td>
                                <td class="py-2">{{ $review->rating }}/5</td>
                                <td class="py-2">{{ $review->review_date }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-500">No reviews to display yet.</p>
            @endif
        </div>
    </div>
</div>
@endsection
