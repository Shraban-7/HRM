@extends('layouts.app')
@section('title', 'Create Payroll Period')
@section('content')
<div class="bg-white p-6 rounded-lg shadow max-w-2xl mx-auto">
    <h3 class="text-lg font-semibold mb-4">Create Payroll Period</h3>
    <form action="{{ route('payrolls.store') }}" method="POST" class="space-y-4">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Period Start *</label>
                <input type="date" name="period_start" value="{{ old('period_start') }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
                @error('period_start')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Period End *</label>
                <input type="date" name="period_end" value="{{ old('period_end') }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
                @error('period_end')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
            </div>
        </div>
        <div class="flex justify-end">
            <a href="{{ route('payrolls.index') }}" class="text-gray-600 px-4 py-2">Cancel</a>
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">Create Period</button>
        </div>
    </form>
</div>
@endsection
