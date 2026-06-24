@extends('layouts.app')
@section('title', 'Feedback')
@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Feedback</h3>
    </div>
    <table class="w-full datatable">
        <thead>
            <tr class="text-left text-sm text-gray-500 border-b">
                <th class="pb-2">ID</th>
                <th class="pb-2">From</th>
                <th class="pb-2">To</th>
                <th class="pb-2">Category</th>
                <th class="pb-2">Message</th>
                <th class="pb-2">Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($feedbacks ?? [] as $fb)
            <tr>
                <td class="py-2">{{ $fb->id }}</td>
                <td class="py-2">{{ $fb->fromUser->username ?? 'N/A' }}</td>
                <td class="py-2">{{ $fb->toUser->username ?? 'N/A' }}</td>
                <td class="py-2">{{ ucfirst($fb->category) }}</td>
                <td class="py-2 max-w-xs truncate">{{ $fb->message }}</td>
                <td class="py-2">{{ $fb->created_at->format('M d, Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection