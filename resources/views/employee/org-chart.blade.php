@extends('layouts.app')
@section('title', 'Organization Chart')
@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-semibold"><i class="fas fa-sitemap mr-2"></i>Organization Chart</h3>
    </div>
    @if(isset($departments) && $departments->count())
        <div class="flex flex-wrap justify-center">
            @foreach($departments as $dept)
                <div class="m-4 p-4 border border-gray-200 rounded-lg min-w-[200px]">
                    <div class="text-center font-bold text-blue-700">{{ $dept->name }}</div>
                    <div class="text-sm text-gray-600 text-center mt-1">Manager: {{ $dept->manager?->username ?? 'Not assigned' }}</div>
                    <div class="text-sm text-gray-500 text-center">{{ $dept->employees_count ?? 0 }} employees</div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-500 text-center">No departments found.</p>
    @endif
</div>
@endsection