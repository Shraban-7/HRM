@extends('layouts.app')
@section('title', 'Goals')
@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Goals</h3>
    </div>
    <table class="w-full datatable">
        <thead>
            <tr class="text-left text-sm text-gray-500 border-b">
                <th class="pb-2">ID</th>
                <th class="pb-2">Employee</th>
                <th class="pb-2">Title</th>
                <th class="pb-2">Target Date</th>
                <th class="pb-2">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($goals ?? [] as $goal)
            <tr>
                <td class="py-2">{{ $goal->id }}</td>
                <td class="py-2">{{ $goal->employee->user->username ?? 'N/A' }}</td>
                <td class="py-2">{{ $goal->title }}</td>
                <td class="py-2">{{ $goal->target_date ?? 'N/A' }}</td>
                <td class="py-2"><span class="px-2 py-1 rounded text-sm {{ $goal->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">{{ ucfirst($goal->status) }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection