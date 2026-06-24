@extends('layouts.app')
@section('title', 'Enroll in Benefits')
@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <h3 class="text-lg font-semibold mb-4">Enroll in Benefits</h3>
    <form action="{{ route('enrollments.store') }}" method="POST" class="space-y-4">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Benefit *</label>
                <select name="benefit_id" class="w-full border border-gray-300 rounded px-3 py-2" required>
                    <option value="">Select Benefit</option>
                    @foreach($benefits ?? [] as $benefit)
                        <option value="{{ $benefit->id }}">{{ $benefit->name }} - ${{ number_format($benefit->cost, 2) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Enrolled At *</label>
                <input type="date" name="enrolled_at" value="{{ now()->toDateString() }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Enroll</button>
    </form>
</div>
@endsection