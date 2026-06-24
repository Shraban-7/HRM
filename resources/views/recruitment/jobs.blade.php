@extends('layouts.app')
@section('title', 'Job Posts')
@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Job Posts</h3>
        <a href="{{ route('jobs.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"><i class="fas fa-plus mr-2"></i>Create Job Post</a>
    </div>
    <table class="w-full datatable">
        <thead>
            <tr class="text-left text-sm text-gray-500 border-b">
                <th class="pb-2">ID</th>
                <th class="pb-2">Title</th>
                <th class="pb-2">Status</th>
                <th class="pb-2">Closing Date</th>
                <th class="pb-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jobs ?? [] as $job)
            <tr>
                <td class="py-2">{{ $job->id }}</td>
                <td class="py-2">{{ $job->title }}</td>
                <td class="py-2"><span class="px-2 py-1 rounded text-sm {{ $job->status === 'open' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">{{ ucfirst($job->status) }}</span></td>
                <td class="py-2">{{ $job->closing_date ?? 'N/A' }}</td>
                <td class="py-2">
                    <a href="{{ route('jobs.show', $job) }}" class="text-blue-600 hover:underline"><i class="fas fa-eye"></i></a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection