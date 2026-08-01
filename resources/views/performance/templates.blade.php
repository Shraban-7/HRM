@extends('layouts.app')
@section('title', 'Review Form Templates')
@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h3 class="text-lg font-semibold">Review Form Templates</h3>
        <button onclick="document.getElementById('template-form').classList.toggle('hidden')" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">New Template</button>
    </div>

    <form id="template-form" method="POST" action="{{ route('performance.templates.store') }}" class="hidden bg-white p-6 rounded-lg shadow">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Template Name</label>
                <input type="text" name="name" class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <input type="text" name="description" class="w-full border border-gray-300 rounded px-3 py-2">
            </div>
        </div>
        <div id="questions">
            <label class="block text-sm font-medium text-gray-700 mb-1">Questions</label>
            <div class="question-row mb-2 flex gap-2">
                <input type="text" name="questions[]" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="e.g. Technical competence" required>
                <button type="button" onclick="this.parentElement.remove()" class="text-red-500 px-2">Remove</button>
            </div>
        </div>
        <div class="flex gap-2 mt-2">
            <button type="button" onclick="addQuestion()" class="text-blue-600 hover:underline text-sm">+ Add question</button>
        </div>
        <div class="mt-4">
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Create Template</button>
        </div>
    </form>

    <div class="bg-white p-6 rounded-lg shadow">
        <table class="w-full">
            <thead>
                <tr class="text-left text-sm text-gray-500 border-b">
                    <th class="pb-2">Name</th>
                    <th class="pb-2">Questions</th>
                    <th class="pb-2">Reviews</th>
                    <th class="pb-2">Status</th>
                    <th class="pb-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($templates as $template)
                    <tr class="border-b">
                        <td class="py-2">{{ $template->name }}</td>
                        <td class="py-2">{{ count($template->questions) }}</td>
                        <td class="py-2">{{ $template->reviews_count }}</td>
                        <td class="py-2">
                            @if($template->is_active)
                                <span class="px-2 py-1 rounded text-sm bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="px-2 py-1 rounded text-sm bg-gray-100 text-gray-800">Inactive</span>
                            @endif
                        </td>
                        <td class="py-2 space-x-2">
                            <form method="POST" action="{{ route('performance.templates.destroy', $template) }}" class="inline" onsubmit="return confirm('Delete this template?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline text-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-4 text-center text-gray-500">No templates yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">{{ $templates->links() }}</div>
    </div>
</div>

<script>
    function addQuestion() {
        const row = document.createElement('div');
        row.className = 'question-row mb-2 flex gap-2';
        row.innerHTML = '<input type="text" name="questions[]" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Question" required><button type="button" onclick="this.parentElement.remove()" class="text-red-500 px-2">Remove</button>';
        document.getElementById('questions').appendChild(row);
    }
</script>
@endsection
