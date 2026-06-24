@extends('layouts.app')
@section('title', 'Attendance Calendar')
@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Attendance Calendar</h3>
        <div class="flex items-center space-x-2">
            <a href="?month={{ $prevMonth ?? '' }}&year={{ $prevYear ?? '' }}" id="calendar-prev" class="px-3 py-1 border rounded hover:bg-gray-100"><i class="fas fa-chevron-left"></i></a>
            <span class="text-lg font-semibold">{{ $monthName ?? now()->format('F Y') }}</span>
            <a href="?month={{ $nextMonth ?? '' }}&year={{ $nextYear ?? '' }}" id="calendar-next" class="px-3 py-1 border rounded hover:bg-gray-100"><i class="fas fa-chevron-right"></i></a>
        </div>
    </div>

    <div class="grid grid-cols-7 gap-px bg-gray-200 text-xs">
        @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
            <div class="bg-gray-50 p-2 text-center font-semibold text-gray-600">{{ $day }}</div>
        @endforeach
        @foreach($calendarDays ?? [] as $day)
            <div class="bg-white p-2 min-h-[80px] {{ $day['is_current_month'] ? '' : 'bg-gray-50 text-gray-400' }}">
                <div class="font-medium">{{ $day['day'] }}</div>
                @if(!empty($day['records']))
                    @foreach($day['records'] as $record)
                        <div class="mt-1 text-xs p-1 rounded {{ $record->status === 'present' ? 'bg-green-100 text-green-800' : ($record->status === 'late' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            {{ \Carbon\Carbon::parse($record->clock_in)->format('H:i') }}
                        </div>
                    @endforeach
                @endif
            </div>
        @endforeach
    </div>
</div>
@endsection