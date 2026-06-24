@extends('layouts.app')
@section('title', 'Benefits')
@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Benefits</h3>
    </div>
    <table class="w-full datatable">
        <thead>
            <tr class="text-left text-sm text-gray-500 border-b">
                <th class="pb-2">ID</th>
                <th class="pb-2">Name</th>
                <th class="pb-2">Type</th>
                <th class="pb-2">Cost</th>
                <th class="pb-2">Active</th>
                <th class="pb-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($benefits ?? [] as $benefit)
            <tr>
                <td class="py-2">{{ $benefit->id }}</td>
                <td class="py-2">{{ $benefit->name }}</td>
                <td class="py-2">{{ ucfirst($benefit->type) }}</td>
                <td class="py-2">${{ number_format($benefit->cost, 2) }}</td>
                <td class="py-2"><span class="px-2 py-1 rounded text-sm {{ $benefit->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">{{ $benefit->is_active ? 'Yes' : 'No' }}</span></td>
                <td class="py-2"><a href="{{ route('enrollments.index') }}" class="text-blue-600 hover:underline">Enroll</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection