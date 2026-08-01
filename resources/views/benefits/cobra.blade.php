@extends('layouts.app')
@section('title', 'COBRA Management')
@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h3 class="text-lg font-semibold">COBRA Management</h3>
        <button onclick="document.getElementById('cobra-form').classList.toggle('hidden')" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">New Coverage</button>
    </div>

    <form id="cobra-form" method="POST" action="{{ route('cobra.store') }}" class="hidden bg-white p-6 rounded-lg shadow">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Employee *</label>
                <select name="employee_id" class="w-full border border-gray-300 rounded px-3 py-2" required>
                    <option value="">Select Employee</option>
                    @foreach(\App\Models\Employee::with('user')->get() as $emp)<option value="{{ $emp->id }}">{{ $emp->user->username ?? 'Employee #'.$emp->id }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Benefit *</label>
                <select name="benefit_id" class="w-full border border-gray-300 rounded px-3 py-2" required>
                    <option value="">Select Benefit</option>
                    @foreach(\App\Models\Benefit::all() as $benefit)<option value="{{ $benefit->id }}">{{ $benefit->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Premium</label>
                <input type="number" name="premium_amount" step="0.01" min="0" class="w-full border border-gray-300 rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Qualifying Event Date *</label>
                <input type="date" name="qualifying_event_date" class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Coverage Start *</label>
                <input type="date" name="coverage_start_date" class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Coverage End *</label>
                <input type="date" name="coverage_end_date" class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Create Coverage</button>
        </div>
    </form>

    <div class="bg-white p-6 rounded-lg shadow">
        <table class="w-full">
            <thead>
                <tr class="text-left text-sm text-gray-500 border-b">
                    <th class="pb-2">Employee</th>
                    <th class="pb-2">Benefit</th>
                    <th class="pb-2">Coverage Period</th>
                    <th class="pb-2">Premium</th>
                    <th class="pb-2">Status</th>
                    <th class="pb-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($coverages as $coverage)
                    <tr class="border-b">
                        <td class="py-2">{{ $coverage->employee->user->username ?? 'N/A' }}</td>
                        <td class="py-2">{{ $coverage->benefit->name ?? 'N/A' }}</td>
                        <td class="py-2 text-sm">{{ $coverage->coverage_start_date->format('M j, Y') }} &rarr; {{ $coverage->coverage_end_date->format('M j, Y') }}</td>
                        <td class="py-2">{{ number_format($coverage->premium_amount, 2) }}</td>
                        <td class="py-2">
                            <span class="px-2 py-1 rounded text-sm
                                @if($coverage->status === 'active') bg-green-100 text-green-800
                                @elseif($coverage->status === 'expired') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">{{ $coverage->status }}</span>
                        </td>
                        <td class="py-2">
                            @if($coverage->status === 'active')
                            <form method="POST" action="{{ route('cobra.terminate', $coverage) }}" class="inline" onsubmit="return confirm('Terminate this COBRA coverage?');">
                                @csrf
                                <button type="submit" class="text-red-600 hover:underline text-sm">Terminate</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-4 text-center text-gray-500">No COBRA coverages.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">{{ $coverages->links() }}</div>
    </div>
</div>
@endsection
