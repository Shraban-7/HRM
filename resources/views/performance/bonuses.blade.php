@extends('layouts.app')
@section('title', 'Performance Bonuses')
@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h3 class="text-lg font-semibold">Performance Bonuses</h3>
        <a href="{{ route('reviews.index') }}" class="text-gray-600 hover:text-gray-800">&larr; Back to Reviews</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-sm text-gray-500">Employees Eligible</div>
            <div class="text-2xl font-bold">{{ count($bonuses) }}</div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-sm text-gray-500">Total Bonus</div>
            <div class="text-2xl font-bold text-green-600">{{ number_format($total, 2) }}</div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow">
        <table class="w-full">
            <thead>
                <tr class="text-left text-sm text-gray-500 border-b">
                    <th class="pb-2">Employee</th>
                    <th class="pb-2">Rating</th>
                    <th class="pb-2">Monthly Salary</th>
                    <th class="pb-2">Bonus</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bonuses as $bonus)
                    <tr class="border-b">
                        <td class="py-2">{{ $bonus['employee_name'] }}</td>
                        <td class="py-2">{{ $bonus['rating'] }}/5</td>
                        <td class="py-2">{{ number_format($bonus['salary'], 2) }}</td>
                        <td class="py-2 font-semibold text-green-600">{{ number_format($bonus['bonus'], 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-4 text-center text-gray-500">No rated reviews yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
