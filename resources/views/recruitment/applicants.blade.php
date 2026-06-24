@extends('layouts.app')
@section('title', 'Applicants')
@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Applicants</h3>
        <a href="{{ route('applicants.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"><i class="fas fa-plus mr-2"></i>Add Applicant</a>
    </div>
    <table class="w-full datatable">
        <thead>
            <tr class="text-left text-sm text-gray-500 border-b">
                <th class="pb-2">ID</th>
                <th class="pb-2">Name</th>
                <th class="pb-2">Email</th>
                <th class="pb-2">Job Post</th>
                <th class="pb-2">Status</th>
                <th class="pb-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($applicants ?? [] as $applicant)
            <tr>
                <td class="py-2">{{ $applicant->id }}</td>
                <td class="py-2">{{ $applicant->name }}</td>
                <td class="py-2">{{ $applicant->email }}</td>
                <td class="py-2">{{ $applicant->jobPost->title ?? 'N/A' }}</td>
                <td class="py-2"><span class="px-2 py-1 rounded text-sm bg-blue-100 text-blue-800">{{ ucfirst($applicant->status) }}</span></td>
                <td class="py-2"><a href="{{ route('applicants.show', $applicant) }}" class="text-blue-600 hover:underline"><i class="fas fa-eye"></i></a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection