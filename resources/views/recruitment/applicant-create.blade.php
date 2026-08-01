@extends('layouts.app')
@section('title', 'Add Applicant')
@section('content')
<div class="bg-white p-6 rounded-lg shadow max-w-2xl mx-auto">
    <h3 class="text-lg font-semibold mb-4">Add Applicant</h3>
    <form action="{{ route('applicants.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Job Post *</label>
            <select name="job_post_id" class="w-full border border-gray-300 rounded px-3 py-2" required>
                <option value="">Select Job</option>
                @foreach($jobs as $job)<option value="{{ $job->id }}" @selected(old('job_post_id') == $job->id)>{{ $job->title }}</option>@endforeach
            </select>
            @error('job_post_id')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
                @error('name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
                @error('email')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
            <input type="text" name="phone" value="{{ old('phone') }}" class="w-full border border-gray-300 rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Resume (PDF, DOC, DOCX &mdash; max 2MB)</label>
            <input type="file" name="resume" accept=".pdf,.doc,.docx" class="w-full border border-gray-300 rounded px-3 py-2">
            @error('resume')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
        </div>
        <div class="flex justify-end">
            <a href="{{ route('applicants.index') }}" class="text-gray-600 px-4 py-2">Cancel</a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Submit Application</button>
        </div>
    </form>
</div>
@endsection
