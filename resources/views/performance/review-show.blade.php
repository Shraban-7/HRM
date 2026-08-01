@extends('layouts.app')
@section('title', 'Performance Review')
@section('content')
<div class="bg-white p-6 rounded-lg shadow max-w-3xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-semibold">Performance Review #{{ $review->id }}</h3>
        <a href="{{ route('reviews.index') }}" class="text-gray-600 hover:text-gray-800"><i class="fas fa-arrow-left mr-1"></i>Back</a>
    </div>

    <div class="grid grid-cols-2 gap-6 border-b pb-4 mb-4">
        <div>
            <p class="text-sm text-gray-500">Employee</p>
            <p class="font-medium">{{ $review->employee->full_name ?? $review->employee->user->username ?? 'N/A' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Reviewer</p>
            <p class="font-medium">{{ $review->reviewer->username ?? 'N/A' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Review Date</p>
            <p class="font-medium">{{ $review->review_date->format('d M Y') }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Status</p>
            <span class="px-2 py-1 rounded text-sm {{ $review->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">{{ ucfirst($review->status) }}</span>
        </div>
    </div>

    <div class="mb-4">
        <p class="text-sm text-gray-500 mb-1">Rating</p>
        @if($review->rating)
            <div class="text-yellow-500">
                @for($i = 1; $i <= 5; $i++)<i class="fas fa-star {{ $i <= $review->rating ? '' : 'text-gray-300' }}"></i>@endfor
                <span class="text-gray-700 ml-2">{{ $review->rating }}/5</span>
            </div>
        @else
            <span class="text-gray-500">No rating provided.</span>
        @endif
    </div>

    <div>
        <p class="text-sm text-gray-500 mb-1">Notes</p>
        <p class="whitespace-pre-line">{{ $review->notes ?? 'No notes provided.' }}</p>
    </div>
</div>
@endsection
