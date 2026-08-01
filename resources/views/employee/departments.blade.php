@extends('layouts.app')
@section('title', 'Departments')
@section('content')
<div class="space-y-6">
    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Departments</h3>
            <button type="button" onclick="document.getElementById('createDeptModal').classList.remove('hidden')" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"><i class="fas fa-plus mr-2"></i>Add Department</button>
        </div>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        <table class="w-full datatable">
            <thead>
                <tr class="text-left text-sm text-gray-500 border-b">
                    <th class="pb-2">ID</th>
                    <th class="pb-2">Name</th>
                    <th class="pb-2">Parent</th>
                    <th class="pb-2">Manager</th>
                    <th class="pb-2">Employees</th>
                    <th class="pb-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($departments as $dept)
                <tr class="border-b">
                    <td class="py-2">{{ $dept->id }}</td>
                    <td class="py-2 font-medium">{{ $dept->name }}</td>
                    <td class="py-2">{{ $dept->parent->name ?? 'N/A' }}</td>
                    <td class="py-2">{{ $dept->manager->username ?? 'N/A' }}</td>
                    <td class="py-2">{{ $dept->employees_count ?? $dept->children_count ?? '0' }}</td>
                    <td class="py-2 space-x-2">
                        <button type="button" onclick="editDept({{ $dept->id }}, '{{ $dept->name }}', {{ $dept->parent_id ?? 'null' }}, {{ $dept->manager_id ?? 'null' }})" class="text-yellow-600 hover:underline"><i class="fas fa-edit"></i></button>
                        <form action="{{ route('departments.destroy', $dept) }}" method="POST" class="inline" onsubmit="return confirm('Delete this department?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">{{ $departments->links() }}</div>
    </div>
</div>

<!-- Create Modal -->
<div id="createDeptModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <h4 class="text-lg font-semibold mb-4">Add Department</h4>
        <form action="{{ route('departments.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                <input type="text" name="name" class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Parent Department</label>
                <select name="parent_id" class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="">None</option>
                    @foreach($departments as $dept)<option value="{{ $dept->id }}">{{ $dept->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Manager</label>
                <select name="manager_id" class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="">None</option>
                    @foreach($managers as $emp)<option value="{{ $emp->user_id }}">{{ $emp->full_name }}</option>@endforeach
                </select>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="this.closest('#createDeptModal').classList.add('hidden')" class="text-gray-600 px-4 py-2">Cancel</button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editDeptModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
        <h4 class="text-lg font-semibold mb-4">Edit Department</h4>
        <form id="editDeptForm" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                <input type="text" id="edit_name" name="name" class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Parent Department</label>
                <select id="edit_parent_id" name="parent_id" class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="">None</option>
                    @foreach($departments as $dept)<option value="{{ $dept->id }}">{{ $dept->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Manager</label>
                <select id="edit_manager_id" name="manager_id" class="w-full border border-gray-300 rounded px-3 py-2">
                    <option value="">None</option>
                    @foreach($managers as $emp)<option value="{{ $emp->user_id }}">{{ $emp->full_name }}</option>@endforeach
                </select>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="this.closest('#editDeptModal').classList.add('hidden')" class="text-gray-600 px-4 py-2">Cancel</button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Save Changes</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function editDept(id, name, parentId, managerId) {
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_parent_id').value = parentId || '';
        document.getElementById('edit_manager_id').value = managerId || '';
        document.getElementById('editDeptForm').action = '{{ route('departments.index') }}/' + id;
        document.getElementById('editDeptModal').classList.remove('hidden');
    }
</script>
@endpush
@endsection
