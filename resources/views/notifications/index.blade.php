@extends('layouts.app')
@section('title', 'Notifications')
@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Notifications</h3>
        <form action="{{ route('notifications.read-all') }}" method="POST">
            @csrf
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Mark All as Read</button>
        </form>
    </div>
    @forelse($notifications as $notification)
    <div class="border-b py-3 flex justify-between items-start {{ $notification->read_at ? 'opacity-60' : '' }}">
        <div>
            <p class="text-gray-800">{{ $notification->data['message'] ?? 'Notification' }}</p>
            <span class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</span>
        </div>
        @if(!$notification->read_at)
        <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
            @csrf
            <button type="submit" class="text-blue-600 text-sm hover:underline">Mark as read</button>
        </form>
        @endif
    </div>
    @empty
    <p class="text-gray-500 text-center py-8">You have no notifications.</p>
    @endforelse
</div>
@endsection
