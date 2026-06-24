@extends('layouts.app')
@section('title', 'Clock In/Out')
@section('content')
<div class="flex items-center justify-center min-h-[60vh]">
    <div class="bg-white p-8 rounded-lg shadow text-center max-w-md w-full">
        <div class="text-6xl mb-4">
            <i class="fas fa-clock text-blue-600"></i>
        </div>
        <h2 class="text-2xl font-bold mb-2">{{ now()->format('l, F j, Y') }}</h2>
        <p id="current-time" class="text-4xl font-mono text-gray-700 mb-6">{{ now()->format('H:i:s') }}</p>

        <div id="attendance-status" class="mb-6">
            @if(isset($hasClockedIn) && $hasClockedIn)
                <span class="text-green-600 font-semibold"><i class="fas fa-check-circle mr-1"></i>Clocked In</span>
                <p id="clock-time" class="text-sm text-gray-500 mt-1">In: {{ $clockInTime ?? '' }}</p>
            @else
                <span class="text-gray-600"><i class="fas fa-clock mr-1"></i>Not Clocked In</span>
            @endif
        </div>

        <div class="space-x-4">
            @if(!isset($hasClockedIn) || !$hasClockedIn)
                <button id="clock-in-btn" data-url="{{ route('attendance.clock-in') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg text-lg hover:bg-blue-700 transition">
                    <i class="fas fa-sign-in-alt mr-2"></i>Clock In
                </button>
            @else
                <button id="clock-out-btn" data-url="{{ route('attendance.clock-out') }}" class="bg-red-600 text-white px-6 py-3 rounded-lg text-lg hover:bg-red-700 transition">
                    <i class="fas fa-sign-out-alt mr-2"></i>Clock Out
                </button>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
setInterval(function() {
    $('#current-time').text(new Date().toLocaleTimeString('en-US', {hour12: false}));
}, 1000);
</script>
@endpush
@endsection