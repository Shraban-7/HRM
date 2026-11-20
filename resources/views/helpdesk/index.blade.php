@extends('layouts.app')

@section('title', 'Internal HR & Support Help Desk')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Internal Support Help Desk</h1>
            <p class="text-sm text-gray-500 mt-1">Employee inquiry routing, payroll clarifications, workplace facilities, and IT support ticketing.</p>
        </div>
        <button onclick="document.getElementById('ticketModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-lg shadow-sm transition flex items-center space-x-2">
            <i class="fas fa-ticket-alt"></i>
            <span>Submit New Ticket</span>
        </button>
    </div>

    <!-- Tickets Ledger -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-wrap items-center justify-between gap-4 bg-gray-50/50">
            <div class="flex items-center space-x-2">
                <a href="{{ route('helpdesk.index') }}" class="text-xs px-2.5 py-1 rounded {{ !request('status') ? 'bg-indigo-600 text-white font-bold' : 'text-gray-600 hover:bg-gray-100' }}">All</a>
                <a href="{{ route('helpdesk.index', ['status' => 'open']) }}" class="text-xs px-2.5 py-1 rounded {{ request('status') === 'open' ? 'bg-blue-600 text-white font-bold' : 'text-gray-600 hover:bg-gray-100' }}">Open</a>
                <a href="{{ route('helpdesk.index', ['status' => 'in_progress']) }}" class="text-xs px-2.5 py-1 rounded {{ request('status') === 'in_progress' ? 'bg-amber-500 text-white font-bold' : 'text-gray-600 hover:bg-gray-100' }}">In Progress</a>
                <a href="{{ route('helpdesk.index', ['status' => 'resolved']) }}" class="text-xs px-2.5 py-1 rounded {{ request('status') === 'resolved' ? 'bg-green-600 text-white font-bold' : 'text-gray-600 hover:bg-gray-100' }}">Resolved</a>
            </div>
            <div class="text-xs text-gray-500">{{ $tickets->total() }} tickets in queue</div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3">Ticket # / Subject</th>
                        <th class="px-6 py-3">Department</th>
                        <th class="px-6 py-3">Employee</th>
                        <th class="px-6 py-3">Priority</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3 text-right">View</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($tickets as $ticket)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <a href="{{ route('helpdesk.show', $ticket) }}" class="font-bold text-gray-900 hover:text-indigo-600 flex items-center space-x-2">
                                    <span class="font-mono text-xs bg-gray-100 px-2 py-0.5 rounded text-gray-700">{{ $ticket->ticket_number }}</span>
                                    <span>{{ $ticket->subject }}</span>
                                </a>
                            </td>
                            <td class="px-6 py-4 text-xs font-semibold text-gray-700">
                                {{ $ticket->department }}
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-700">
                                {{ $ticket->employee->user->username ?? 'Employee #'.$ticket->employee_id }}
                            </td>
                            <td class="px-6 py-4 text-xs">
                                @if($ticket->priority === 'urgent')
                                    <span class="px-2 py-0.5 rounded bg-red-100 text-red-800 font-bold uppercase text-[10px]">Urgent</span>
                                @elseif($ticket->priority === 'high')
                                    <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 font-bold uppercase text-[10px]">High</span>
                                @elseif($ticket->priority === 'low')
                                    <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700 text-[10px]">Low</span>
                                @else
                                    <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-[10px]">Medium</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($ticket->status === 'resolved')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">Resolved</span>
                                @elseif($ticket->status === 'in_progress')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">In Progress</span>
                                @elseif($ticket->status === 'closed')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700">Closed</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">Open</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500">
                                {{ $ticket->created_at->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('helpdesk.show', $ticket) }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                                    Manage &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-400">No support tickets found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $tickets->links() }}
        </div>
    </div>
</div>

<!-- Submit Ticket Modal -->
<div id="ticketModal" class="fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Create Support Ticket</h3>
        <form action="{{ route('helpdesk.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Employee</label>
                <select name="employee_id" class="w-full px-3 py-2 border rounded-lg text-sm" required>
                    <option value="">Select Employee...</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->user->username ?? 'ID #'.$emp->id }} ({{ $emp->employee_id }})</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Department</label>
                    <select name="department" class="w-full px-3 py-2 border rounded-lg text-sm">
                        <option value="HR">Human Resources</option>
                        <option value="Payroll">Payroll / Finance</option>
                        <option value="IT">IT Support</option>
                        <option value="Facilities">Workplace Facilities</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Priority</label>
                    <select name="priority" class="w-full px-3 py-2 border rounded-lg text-sm">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Subject</label>
                <input type="text" name="subject" required placeholder="Tax discrepancy on last payslip" class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="3" required placeholder="Detailed description of issue..." class="w-full px-3 py-2 border rounded-lg text-sm"></textarea>
            </div>
            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="document.getElementById('ticketModal').classList.add('hidden')" class="px-4 py-2 border rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-bold hover:bg-indigo-700">Submit Ticket</button>
            </div>
        </form>
    </div>
</div>
@endsection
