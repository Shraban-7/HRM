@extends('layouts.app')
@section('title', 'Applicant: ' . $applicant->name)
@section('content')
<div class="space-y-6">
    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h3 class="text-lg font-semibold">{{ $applicant->name }}</h3>
                <p class="text-sm text-gray-500">{{ $applicant->email }} @if($applicant->phone) &middot; {{ $applicant->phone }} @endif</p>
            </div>
            <span class="px-2 py-1 rounded text-sm {{ $applicant->status === 'hired' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">{{ ucfirst($applicant->status) }}</span>
        </div>
        <div class="grid grid-cols-2 gap-6">
            <div>
                <h4 class="font-semibold mb-2">Applied For</h4>
                <p class="text-gray-600">{{ $applicant->jobPost->title ?? 'N/A' }}</p>
            </div>
            <div>
                <h4 class="font-semibold mb-2">Resume</h4>
                @if($applicant->resume_path)
                    <a href="{{ \Illuminate\Support\Facades\Storage::url($applicant->resume_path) }}" class="text-blue-600 hover:underline" target="_blank"><i class="fas fa-file-pdf mr-1"></i>Download Resume</a>
                @else
                    <span class="text-gray-500">No resume uploaded</span>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex justify-between items-center mb-4">
            <h4 class="font-semibold">Interview</h4>
            @if(!$applicant->interview)
                <a href="{{ route('interviews.create', $applicant) }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm"><i class="fas fa-calendar mr-1"></i>Schedule Interview</a>
            @endif
        </div>
        @if($applicant->interview)
            <table class="w-full">
                <tr class="border-b"><td class="py-2 text-gray-500">Scheduled At</td><td class="py-2">{{ $applicant->interview->scheduled_at->format('d M Y H:i') }}</td></tr>
                <tr class="border-b"><td class="py-2 text-gray-500">Status</td><td class="py-2">{{ ucfirst($applicant->interview->status) }}</td></tr>
                <tr><td class="py-2 text-gray-500">Notes</td><td class="py-2">{{ $applicant->interview->notes ?? 'N/A' }}</td></tr>
            </table>
        @else
            <p class="text-gray-500">No interview scheduled yet.</p>
        @endif
    </div>
</div>
@endsection
