@extends('layouts.app')
@section('title', $job->title)
@section('content')
<div class="space-y-6">
    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h3 class="text-lg font-semibold">{{ $job->title }}</h3>
                <p class="text-sm text-gray-500">Posted {{ $job->created_at->diffForHumans() }} &middot; Closing {{ $job->closing_date ?? 'N/A' }}</p>
            </div>
            <span class="px-2 py-1 rounded text-sm {{ $job->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">{{ ucfirst($job->status) }}</span>
        </div>
        <div class="grid grid-cols-2 gap-6">
            <div>
                <h4 class="font-semibold mb-2">Description</h4>
                <p class="text-gray-600 whitespace-pre-line">{{ $job->description }}</p>
            </div>
            <div>
                <h4 class="font-semibold mb-2">Requirements</h4>
                <p class="text-gray-600 whitespace-pre-line">{{ $job->requirements ?? 'Not specified' }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex justify-between items-center mb-4">
            <h4 class="font-semibold">Applicants ({{ $job->applicants->count() }})</h4>
            <a href="{{ route('applicants.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm"><i class="fas fa-plus mr-1"></i>Add Applicant</a>
        </div>
        @if($job->applicants->isEmpty())
            <p class="text-gray-500">No applicants yet.</p>
        @else
            <table class="w-full">
                <thead>
                    <tr class="text-left text-sm text-gray-500 border-b">
                        <th class="pb-2">Name</th>
                        <th class="pb-2">Email</th>
                        <th class="pb-2">Status</th>
                        <th class="pb-2">Interview</th>
                        <th class="pb-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($job->applicants as $applicant)
                    <tr class="border-b">
                        <td class="py-2">{{ $applicant->name }}</td>
                        <td class="py-2">{{ $applicant->email }}</td>
                        <td class="py-2">{{ ucfirst($applicant->status) }}</td>
                        <td class="py-2">{{ $applicant->interview ? $applicant->interview->scheduled_at->format('d M Y H:i') : 'Not scheduled' }}</td>
                        <td class="py-2"><a href="{{ route('applicants.show', $applicant) }}" class="text-blue-600 hover:underline"><i class="fas fa-eye"></i></a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
