@extends('layouts.app')
@section('title', 'Create Performance Review')
@section('content')
<div class="bg-white p-6 rounded-lg shadow max-w-2xl mx-auto">
    <h3 class="text-lg font-semibold mb-4">Create Performance Review</h3>
    <form action="{{ route('reviews.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Employee *</label>
            <select name="employee_id" class="w-full border border-gray-300 rounded px-3 py-2" required>
                <option value="">Select Employee</option>
                @foreach($employees as $emp)<option value="{{ $emp->id }}" @selected(old('employee_id') == $emp->id)>{{ $emp->full_name }}</option>@endforeach
            </select>
            @error('employee_id')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Review Date *</label>
            <input type="date" name="review_date" value="{{ old('review_date') }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
            @error('review_date')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Rating (1-5)</label>
            <select name="rating" class="w-full border border-gray-300 rounded px-3 py-2">
                <option value="">No Rating</option>
                @for($i = 1; $i <= 5; $i++)<option value="{{ $i }}" @selected(old('rating') == $i)>{{ $i }} - {{ ['Poor', 'Below Average', 'Average', 'Good', 'Excellent'][$i - 1] }}</option>@endfor
            </select>
            @error('rating')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
            <textarea name="notes" rows="4" class="w-full border border-gray-300 rounded px-3 py-2">{{ old('notes') }}</textarea>
        </div>
        <div class="flex justify-end">
            <a href="{{ route('reviews.index') }}" class="text-gray-600 px-4 py-2">Cancel</a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Submit Review</button>
        </div>
    </form>
</div>
@endsection
