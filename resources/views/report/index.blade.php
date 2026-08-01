@extends('layouts.app')
@section('title', 'Reports')
@section('content')
<div class="bg-white p-6 rounded-lg shadow max-w-2xl">
    <h3 class="text-lg font-semibold mb-4">Report Builder</h3>
    <form method="POST" action="{{ route('reports.generate') }}">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Report Type</label>
                <select name="type" class="w-full border border-gray-300 rounded px-3 py-2" required>
                    @foreach($reportTypes as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Period Start (optional)</label>
                <input type="date" name="period_start" class="w-full border border-gray-300 rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Period End (optional)</label>
                <input type="date" name="period_end" class="w-full border border-gray-300 rounded px-3 py-2">
            </div>
        </div>
        <div class="mt-6">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Generate Report</button>
        </div>
    </form>
</div>
@endsection
