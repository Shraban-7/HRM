@extends('layouts.app')

@section('title', 'Ticket #' . $ticket->ticket_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('helpdesk.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900 flex items-center space-x-1">
            <i class="fas fa-arrow-left text-xs"></i>
            <span>Back to All Tickets</span>
        </a>
        <div class="flex items-center space-x-2">
            <span class="font-mono text-xs bg-gray-100 text-gray-800 px-3 py-1 rounded font-bold">{{ $ticket->ticket_number }}</span>
            <span class="text-xs px-2.5 py-1 rounded-full font-bold uppercase bg-blue-100 text-blue-800">{{ $ticket->status }}</span>
        </div>
    </div>

    <!-- Ticket Detail Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-gray-100 gap-2">
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ $ticket->subject }}</h1>
                <div class="text-xs text-gray-500 mt-1">
                    Submitted by <strong class="text-gray-800">{{ $ticket->employee->user->username ?? 'Employee #'.$ticket->employee_id }}</strong> &bull; Dept: <span class="font-semibold text-indigo-600">{{ $ticket->department }}</span> &bull; {{ $ticket->created_at->format('M d, Y h:i A') }}
                </div>
            </div>
            <div>
                <span class="text-xs uppercase font-extrabold px-2.5 py-1 rounded {{ $ticket->priority === 'urgent' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800' }}">
                    {{ $ticket->priority }} Priority
                </span>
            </div>
        </div>

        <div class="mt-4 text-sm text-gray-700 leading-relaxed whitespace-pre-line bg-gray-50 p-4 rounded-lg border border-gray-100">
            {{ $ticket->description }}
        </div>
    </div>

    <!-- Ticket Comments & Activity Trail -->
    <div class="space-y-4">
        <h2 class="text-base font-bold text-gray-800">Conversation & Resolution Trail</h2>

        @forelse($ticket->comments as $comment)
            <div class="bg-white rounded-xl shadow-sm border {{ $comment->is_internal ? 'border-amber-200 bg-amber-50/20' : 'border-gray-200' }} p-4">
                <div class="flex items-center justify-between text-xs mb-2">
                    <div class="flex items-center space-x-2">
                        <span class="font-bold text-gray-900">{{ $comment->user->username ?? 'Agent' }}</span>
                        @if($comment->is_internal)
                            <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-1.5 py-0.5 rounded uppercase">Internal Staff Note</span>
                        @endif
                    </div>
                    <span class="text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                </div>
                <div class="text-xs text-gray-700 leading-relaxed whitespace-pre-line">
                    {{ $comment->comment }}
                </div>
            </div>
        @empty
            <div class="bg-white p-6 rounded-xl border border-gray-200 text-center text-gray-400 text-xs">
                No comments or updates recorded yet.
            </div>
        @endforelse

        <!-- Reply Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-sm font-bold text-gray-900 mb-3">Add Response / Update Status</h3>
            <form action="{{ route('helpdesk.comments.store', $ticket) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <textarea name="comment" rows="3" required placeholder="Type reply message or internal resolution note..." class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center space-x-4">
                        <div>
                            <label class="block text-[11px] font-semibold uppercase text-gray-600 mb-1">Update Status</label>
                            <select name="status" class="px-2.5 py-1.5 border rounded-lg text-xs font-semibold">
                                <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                                <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                        <label class="flex items-center space-x-2 text-xs text-gray-700 mt-4">
                            <input type="checkbox" name="is_internal" value="1" class="rounded text-amber-600">
                            <span>Internal Note (Hidden from employee)</span>
                        </label>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-lg shadow-sm transition">
                        Post Response
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
