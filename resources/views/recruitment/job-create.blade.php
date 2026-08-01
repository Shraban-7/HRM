@extends('layouts.app')
@section('title', 'Create Job Post')
@section('content')
<div class="bg-white p-6 rounded-lg shadow max-w-2xl mx-auto">
    <h3 class="text-lg font-semibold mb-4">Create Job Post</h3>
    <form action="{{ route('jobs.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Job Title *</label>
            <input type="text" name="title" value="{{ old('title') }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
            @error('title')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
            <textarea name="description" rows="4" class="w-full border border-gray-300 rounded px-3 py-2" required>{{ old('description') }}</textarea>
            @error('description')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Requirements</label>
            <textarea name="requirements" rows="3" class="w-full border border-gray-300 rounded px-3 py-2">{{ old('requirements') }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Closing Date</label>
            <input type="date" name="closing_date" value="{{ old('closing_date') }}" class="w-full border border-gray-300 rounded px-3 py-2">
            @error('closing_date')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
        </div>
        <div class="flex justify-end">
            <a href="{{ route('jobs.index') }}" class="text-gray-600 px-4 py-2">Cancel</a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Create Job Post</button>
        </div>
    </form>
</div>
@endsection
