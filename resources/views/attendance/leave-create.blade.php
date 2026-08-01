@extends('layouts.app')
@section('title', 'Apply for Leave')
@section('content')
<div class="bg-white p-6 rounded-lg shadow max-w-2xl mx-auto">
    <h3 class="text-lg font-semibold mb-4">Apply for Leave</h3>
    <form action="{{ route('leave.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Leave Type *</label>
            <select name="type" class="w-full border border-gray-300 rounded px-3 py-2" required>
                <option value="">Select Type</option>
                <option value="annual" @selected(old('type') === 'annual')>Annual</option>
                <option value="sick" @selected(old('type') === 'sick')>Sick</option>
                <option value="other" @selected(old('type') === 'other')>Other</option>
            </select>
            @error('type')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date *</label>
                <input type="date" name="start_date" value="{{ old('start_date') }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
                @error('start_date')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">End Date *</label>
                <input type="date" name="end_date" value="{{ old('end_date') }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
                @error('end_date')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="flex justify-end">
            <a href="{{ route('leave.index') }}" class="text-gray-600 px-4 py-2">Back</a>
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">Submit Request</button>
        </div>
    </form>
</div>
@endsection
