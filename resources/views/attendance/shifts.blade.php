@extends('layouts.app')
@section('title', 'Shift Management')
@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-semibold">Shift Management</h3>
        <button onclick="document.getElementById('shift-form').classList.toggle('hidden')" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Shift</button>
    </div>

    <form id="shift-form" method="POST" action="{{ route('shifts.store') }}" class="hidden mb-6 border border-gray-200 rounded p-4">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" name="name" class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                <input type="time" name="start_time" class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                <input type="time" name="end_time" class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                <input type="text" name="color" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="#3B82F6">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Create</button>
            </div>
        </div>
    </form>

    <table class="w-full">
        <thead>
            <tr class="text-left text-sm text-gray-500 border-b">
                <th class="pb-2">Shift</th>
                <th class="pb-2">Start Time</th>
                <th class="pb-2">End Time</th>
                <th class="pb-2">Status</th>
                <th class="pb-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($shifts as $shift)
                <tr class="border-b">
                    <td class="py-2">{{ $shift->name }}</td>
                    <td class="py-2">{{ $shift->start_time }}</td>
                    <td class="py-2">{{ $shift->end_time }}</td>
                    <td class="py-2">
                        @if($shift->is_active)
                            <span class="px-2 py-1 rounded text-sm bg-green-100 text-green-800">Active</span>
                        @else
                            <span class="px-2 py-1 rounded text-sm bg-gray-100 text-gray-800">Inactive</span>
                        @endif
                    </td>
                    <td class="py-2 space-x-2">
                        <form method="POST" action="{{ route('shifts.update', $shift) }}" class="inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" name="is_active" value="{{ $shift->is_active ? 0 : 1 }}" class="text-blue-600 hover:underline text-sm">
                                {{ $shift->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('shifts.destroy', $shift) }}" class="inline" onsubmit="return confirm('Delete this shift?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline text-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-4 text-center text-gray-500">No shifts configured yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-4">{{ $shifts->links() }}</div>
</div>
@endsection
