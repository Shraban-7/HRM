@extends('layouts.app')
@section('title', 'Interviews')
@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Interviews</h3>
        <a href="{{ route('interviews.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"><i class="fas fa-plus mr-2"></i>Schedule Interview</a>
    </div>
    <table class="w-full datatable">
        <thead>
            <tr class="text-left text-sm text-gray-500 border-b">
                <th class="pb-2">ID</th>
                <th class="pb-2">Applicant</th>
                <th class="pb-2">Job Post</th>
                <th class="pb-2">Scheduled</th>
                <th class="pb-2">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($interviews ?? [] as $interview)
            <tr>
                <td class="py-2">{{ $interview->id }}</td>
                <td class="py-2">{{ $interview->applicant->name ?? 'N/A' }}</td>
                <td class="py-2">{{ $interview->jobPost->title ?? 'N/A' }}</td>
                <td class="py-2">{{ \Carbon\Carbon::parse($interview->scheduled_at)->format('M d, Y H:i') }}</td>
                <td class="py-2"><span class="px-2 py-1 rounded text-sm bg-blue-100 text-blue-800">{{ ucfirst($interview->status) }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection