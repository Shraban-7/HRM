@extends('layouts.app')
@section('title', 'Schedule Interview')
@section('content')
<div class="bg-white p-6 rounded-lg shadow max-w-2xl mx-auto">
    <h3 class="text-lg font-semibold mb-4">Schedule Interview</h3>
    <form action="{{ route('interviews.store') }}" method="POST" class="space-y-4">
        @csrf
        <input type="hidden" name="applicant_id" value="{{ $applicant->id }}">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Applicant *</label>
            <input type="text" value="{{ $applicant->name }}" class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-50" disabled>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Job Post *</label>
            <select name="job_post_id" class="w-full border border-gray-300 rounded px-3 py-2" required>
                @foreach(\App\Models\JobPost::all() as $job)<option value="{{ $job->id }}" @selected($job->id == $applicant->job_post_id)>{{ $job->title }}</option>@endforeach
            </select>
            @error('job_post_id')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Scheduled At *</label>
            <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
            @error('scheduled_at')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
        </div>
        <div class="flex justify-end">
            <a href="{{ route('interviews.index') }}" class="text-gray-600 px-4 py-2">Cancel</a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Schedule Interview</button>
        </div>
    </form>
</div>
@endsection
