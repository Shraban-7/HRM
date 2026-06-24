@extends('layouts.app')
@section('title', 'Audit Logs')
@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Audit Logs</h3>
    </div>
    <table class="w-full datatable">
        <thead>
            <tr class="text-left text-sm text-gray-500 border-b">
                <th class="pb-2">ID</th>
                <th class="pb-2">User</th>
                <th class="pb-2">Action</th>
                <th class="pb-2">Resource</th>
                <th class="pb-2">Resource ID</th>
                <th class="pb-2">IP Address</th>
                <th class="pb-2">Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
            <tr>
                <td class="py-2">{{ $log->id }}</td>
                <td class="py-2">{{ $log->user->username ?? 'System' }}</td>
                <td class="py-2"><span class="px-2 py-1 rounded text-sm bg-blue-100 text-blue-800">{{ $log->action }}</span></td>
                <td class="py-2">{{ $log->resource }}</td>
                <td class="py-2">{{ $log->resource_id ?? 'N/A' }}</td>
                <td class="py-2">{{ $log->ip_address ?? 'N/A' }}</td>
                <td class="py-2">{{ $log->created_at?->format('M d, Y H:i') ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-4">{{ $logs->links() }}</div>
</div>
@endsection