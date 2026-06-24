@extends('layouts.app')
@section('title', 'Performance Reviews')
@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Performance Reviews</h3>
        <a href="{{ route('reviews.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"><i class="fas fa-plus mr-2"></i>New Review</a>
    </div>
    <table class="w-full datatable">
        <thead>
            <tr class="text-left text-sm text-gray-500 border-b">
                <th class="pb-2">ID</th>
                <th class="pb-2">Employee</th>
                <th class="pb-2">Reviewer</th>
                <th class="pb-2">Date</th>
                <th class="pb-2">Rating</th>
                <th class="pb-2">Status</th>
                <th class="pb-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reviews ?? [] as $review)
            <tr>
                <td class="py-2">{{ $review->id }}</td>
                <td class="py-2">{{ $review->employee->user->username ?? 'N/A' }}</td>
                <td class="py-2">{{ $review->reviewer->username ?? 'N/A' }}</td>
                <td class="py-2">{{ $review->review_date }}</td>
                <td class="py-2">{{ $review->rating ?? 'N/A' }}/5</td>
                <td class="py-2"><span class="px-2 py-1 rounded text-sm {{ $review->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">{{ ucfirst($review->status) }}</span></td>
                <td class="py-2"><a href="{{ route('reviews.show', $review) }}" class="text-blue-600 hover:underline"><i class="fas fa-eye"></i></a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection