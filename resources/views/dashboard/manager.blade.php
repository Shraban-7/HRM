@extends('layouts.app')
@section('title', 'Manager Dashboard')
@section('content')
<div class="space-y-6">
    <h2 class="text-xl font-semibold">Manager Dashboard</h2>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-lg shadow">
            <h4 class="text-sm text-gray-500">Team Members</h4>
            <p class="text-2xl font-bold text-blue-600">{{ $teamCount ?? 0 }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <h4 class="text-sm text-gray-500">Pending Leave Approvals</h4>
            <p class="text-2xl font-bold text-yellow-600">{{ $pendingLeaves ?? 0 }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <h4 class="text-sm text-gray-500">Team Attendance Today</h4>
            <p class="text-2xl font-bold text-green-600">{{ $teamAttendance ?? 0 }}</p>
        </div>
        <div class="bg-white p-4 rounded-lg shadow">
            <h4 class="text-sm text-gray-500">Open Performance Reviews</h4>
            <p class="text-2xl font-bold text-purple-600">{{ $pendingReviews ?? 0 }}</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('leave.index') }}" class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700"><i class="fas fa-calendar-check mr-2"></i>Review Leave Requests</a>
            <a href="{{ route('reviews.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"><i class="fas fa-star mr-2"></i>Performance Reviews</a>
            <a href="{{ route('employees.index') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"><i class="fas fa-user-friends mr-2"></i>My Team</a>
            <a href="{{ route('goals.index') }}" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700"><i class="fas fa-bullseye mr-2"></i>Team Goals</a>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Recent Activity</h3>
        @if(isset($recentActivity) && count($recentActivity))
            <div class="space-y-3">
                @foreach($recentActivity as $activity)
                    <div class="flex items-center space-x-3 text-sm border-b pb-2">
                        <span class="text-gray-500">{{ $activity['time'] ?? '' }}</span>
                        <span>{{ $activity['description'] ?? '' }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500">No recent activity.</p>
        @endif
    </div>
</div>
@endsection