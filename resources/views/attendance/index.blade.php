@extends('layouts.app')
@section('title', 'Attendance')
@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Attendance Records</h3>
        <div class="space-x-2">
            <a href="{{ route('attendance.clock-in') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"><i class="fas fa-sign-in-alt mr-1"></i>Clock In</a>
            <a href="{{ route('attendance.calendar') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700"><i class="fas fa-calendar mr-1"></i>Calendar</a>
        </div>
    </div>
    <table id="attendance-table" class="w-full">
        <thead>
            <tr class="text-left text-sm text-gray-500 border-b">
                <th class="pb-2">ID</th>
                <th class="pb-2">Employee</th>
                <th class="pb-2">Date</th>
                <th class="pb-2">Clock In</th>
                <th class="pb-2">Clock Out</th>
                <th class="pb-2">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendance ?? [] as $record)
            <tr>
                <td class="py-2">{{ $record->id }}</td>
                <td class="py-2">{{ $record->employee->user->username ?? 'N/A' }}</td>
                <td class="py-2">{{ $record->date }}</td>
                <td class="py-2">{{ $record->clock_in ? \Carbon\Carbon::parse($record->clock_in)->format('H:i') : 'N/A' }}</td>
                <td class="py-2">{{ $record->clock_out ? \Carbon\Carbon::parse($record->clock_out)->format('H:i') : 'N/A' }}</td>
                <td class="py-2"><span class="px-2 py-1 rounded text-sm {{ $record->status === 'present' ? 'bg-green-100 text-green-800' : ($record->status === 'late' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">{{ ucfirst($record->status) }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection