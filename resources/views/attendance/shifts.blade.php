@extends('layouts.app')
@section('title', 'Shift Management')
@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <h3 class="text-lg font-semibold mb-4">Shift Management</h3>
    <p class="text-gray-500 mb-6">Shift rosters and assignment management will be available in the next phase.</p>

    <table class="w-full">
        <thead>
            <tr class="text-left text-sm text-gray-500 border-b">
                <th class="pb-2">Shift</th>
                <th class="pb-2">Start Time</th>
                <th class="pb-2">End Time</th>
                <th class="pb-2">Status</th>
            </tr>
        </thead>
        <tbody>
            <tr class="border-b">
                <td class="py-2">Morning</td>
                <td class="py-2">08:00</td>
                <td class="py-2">16:00</td>
                <td class="py-2"><span class="px-2 py-1 rounded text-sm bg-green-100 text-green-800">Active</span></td>
            </tr>
            <tr class="border-b">
                <td class="py-2">Evening</td>
                <td class="py-2">14:00</td>
                <td class="py-2">22:00</td>
                <td class="py-2"><span class="px-2 py-1 rounded text-sm bg-green-100 text-green-800">Active</span></td>
            </tr>
            <tr class="border-b">
                <td class="py-2">Night</td>
                <td class="py-2">22:00</td>
                <td class="py-2">06:00</td>
                <td class="py-2"><span class="px-2 py-1 rounded text-sm bg-green-100 text-green-800">Active</span></td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
