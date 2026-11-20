@extends('layouts.app')

@section('title', 'Employee Onboarding Portal')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Employee Onboarding</h1>
            <p class="text-sm text-gray-500 mt-1">Manage structured checklists, IT setup, and new hire orientation workflows.</p>
        </div>
        <button onclick="document.getElementById('checklistModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-lg shadow-sm transition flex items-center space-x-2">
            <i class="fas fa-plus"></i>
            <span>Create Checklist Template</span>
        </button>
    </div>

    <!-- Checklists Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @forelse($checklists as $checklist)
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded">
                            {{ $checklist->department->name ?? 'General / All Depts' }}
                        </span>
                        <span class="text-xs text-gray-400 font-semibold">{{ $checklist->tasks->count() }} Tasks</span>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 mt-2">{{ $checklist->title }}</h3>
                    <p class="text-xs text-gray-500 mt-1">{{ $checklist->description ?? 'Standard onboarding tasks template.' }}</p>
                    
                    <div class="mt-4 space-y-1.5 border-t border-gray-100 pt-3">
                        @foreach($checklist->tasks->take(4) as $task)
                            <div class="text-xs text-gray-600 flex items-center space-x-2">
                                <i class="fas fa-check-circle text-gray-300 text-[10px]"></i>
                                <span class="truncate">{{ $task->title }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-100 flex justify-end">
                    <button onclick="openAddTaskModal({{ $checklist->id }}, '{{ addslashes($checklist->title) }}')" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                        + Add Task to Template
                    </button>
                </div>
            </div>
        @empty
            <div class="md:col-span-3 bg-white p-8 rounded-xl text-center text-gray-400 text-sm border border-gray-200">
                No onboarding checklist templates configured yet. Click above to create one.
            </div>
        @endforelse
    </div>

    <!-- Active Onboarding Progress Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50/50">
            <h2 class="text-base font-bold text-gray-800">New Hire Onboarding Tracker</h2>
            <span class="text-xs text-gray-500">Tracking all active employees</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3">Employee</th>
                        <th class="px-6 py-3">Department</th>
                        <th class="px-6 py-3">Hire Date</th>
                        <th class="px-6 py-3">Onboarding Progress</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($employees as $emp)
                        @php
                            $totalTasks = $emp->onboardingProgress->count();
                            $completedTasks = $emp->onboardingProgress->where('is_completed', true)->count();
                            $percent = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">{{ $emp->user->username ?? 'Employee #'.$emp->id }}</div>
                                <div class="text-xs text-gray-500">{{ $emp->employee_id }} &bull; {{ $emp->designation }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs font-medium text-gray-700">
                                {{ $emp->department->name ?? 'Unassigned' }}
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-600">
                                {{ $emp->hire_date ? \Carbon\Carbon::parse($emp->hire_date)->format('M d, Y') : '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="w-48 bg-gray-200 rounded-full h-2.5 overflow-hidden">
                                    <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ $percent }}%"></div>
                                </div>
                                <span class="text-xs text-gray-500 mt-1 inline-block">{{ $completedTasks }}/{{ $totalTasks }} tasks ({{ $percent }}%)</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($percent === 100 && $totalTasks > 0)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">Completed</span>
                                @elseif($totalTasks > 0)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">In Progress</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700">Pending Setup</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">No employees found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $employees->links() }}
        </div>
    </div>
</div>

<!-- Checklist Modal -->
<div id="checklistModal" class="fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Create Checklist Template</h3>
        <form action="{{ route('onboarding.checklists.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Checklist Title</label>
                <input type="text" name="title" required placeholder="e.g. Standard Software Engineering Onboarding" class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="2" class="w-full px-3 py-2 border rounded-lg text-sm"></textarea>
            </div>
            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="document.getElementById('checklistModal').classList.add('hidden')" class="px-4 py-2 border rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-bold hover:bg-indigo-700">Save Checklist</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Task Modal -->
<div id="addTaskModal" class="fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl">
        <h3 class="text-lg font-bold text-gray-900 mb-1">Add Task</h3>
        <p id="taskModalSubtitle" class="text-xs text-gray-500 mb-4"></p>
        <form id="addTaskForm" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Task Title</label>
                <input type="text" name="title" required placeholder="e.g. Provision GitHub & Slack access" class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Assigned Role</label>
                <select name="assigned_role" class="w-full px-3 py-2 border rounded-lg text-sm">
                    <option value="employee">Employee</option>
                    <option value="hr_admin">HR Operations</option>
                    <option value="manager">Department Manager</option>
                    <option value="it_support">IT Support</option>
                </select>
            </div>
            <label class="flex items-center space-x-2 text-xs text-gray-700">
                <input type="checkbox" name="is_required" value="1" checked class="rounded text-indigo-600">
                <span>Mandatory requirement for completion</span>
            </label>
            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="document.getElementById('addTaskModal').classList.add('hidden')" class="px-4 py-2 border rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-bold hover:bg-indigo-700">Add Task</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddTaskModal(id, title) {
    document.getElementById('taskModalSubtitle').innerText = 'Template: ' + title;
    document.getElementById('addTaskForm').action = '/onboarding/checklists/' + id + '/tasks';
    document.getElementById('addTaskModal').classList.remove('hidden');
}
</script>
@endsection
