@extends('layouts.app')

@section('title', 'Employee Exit & Offboarding')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Exit & Offboarding Management</h1>
            <p class="text-sm text-gray-500 mt-1">Manage employee resignations, cross-department clearance sign-offs, and final settlements.</p>
        </div>
        <button onclick="document.getElementById('exitModal').classList.remove('hidden')" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-medium text-sm rounded-lg shadow-sm transition flex items-center space-x-2">
            <i class="fas fa-user-minus"></i>
            <span>Log Resignation / Exit</span>
        </button>
    </div>

    <!-- Active Exit Requests -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50/50">
            <h2 class="text-base font-bold text-gray-800">Exit & Clearance Requests</h2>
            <span class="text-xs text-gray-500">{{ $exitRequests->total() }} total recorded</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3">Employee</th>
                        <th class="px-6 py-3">Dates</th>
                        <th class="px-6 py-3">Department Clearance Matrix</th>
                        <th class="px-6 py-3">Exit Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($exitRequests as $exit)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">{{ $exit->employee->user->username ?? 'Employee #'.$exit->employee_id }}</div>
                                <div class="text-xs text-gray-500">{{ $exit->employee->employee_id }} &bull; {{ $exit->employee->department->name ?? 'General' }}</div>
                                @if($exit->reason)
                                    <div class="text-[11px] text-gray-400 mt-1 italic">Reason: {{ Str::limit($exit->reason, 40) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <div><span class="text-gray-400">Resigned:</span> {{ $exit->resignation_date->format('M d, Y') }}</div>
                                <div class="font-semibold text-rose-600 mt-0.5"><span class="text-gray-400">Last Day:</span> {{ $exit->last_working_day->format('M d, Y') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-2">
                                    @foreach($exit->clearances as $clearance)
                                        <div class="inline-flex items-center space-x-1.5 px-2 py-1 rounded text-xs border {{ $clearance->status === 'cleared' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-amber-50 border-amber-200 text-amber-700' }}">
                                            <span>{{ $clearance->department }}:</span>
                                            <span class="font-bold capitalize">{{ $clearance->status }}</span>
                                            @if($clearance->status !== 'cleared')
                                                <form action="{{ route('exit.clearances.clear', $clearance) }}" method="POST" class="inline ml-1">
                                                    @csrf
                                                    <button type="submit" class="text-blue-600 hover:underline text-[10px] font-bold">[Sign Off]</button>
                                                </form>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($exit->status === 'completed')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">Offboarded</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Clearance Pending</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-400">No active exit requests logged.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $exitRequests->links() }}
        </div>
    </div>
</div>

<!-- Exit Log Modal -->
<div id="exitModal" class="fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Log Employee Exit / Resignation</h3>
        <form action="{{ route('exit.store') }}" method="POST" class="space-y-4">
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
                    <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Notice Date</label>
                    <input type="date" name="resignation_date" required class="w-full px-3 py-2 border rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Last Working Day</label>
                    <input type="date" name="last_working_day" required class="w-full px-3 py-2 border rounded-lg text-sm">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Resignation Reason</label>
                <textarea name="reason" rows="2" class="w-full px-3 py-2 border rounded-lg text-sm" placeholder="Career change, relocation, etc."></textarea>
            </div>
            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="document.getElementById('exitModal').classList.add('hidden')" class="px-4 py-2 border rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-rose-600 text-white rounded-lg text-xs font-bold hover:bg-rose-700">Submit Exit Request</button>
            </div>
        </form>
    </div>
</div>
@endsection
