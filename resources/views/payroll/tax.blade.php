@extends('layouts.app')
@section('title', 'Tax Brackets')
@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <h3 class="text-lg font-semibold mb-4">Income Tax Brackets</h3>
    <table class="w-full datatable">
        <thead>
            <tr class="text-left text-sm text-gray-500 border-b">
                <th class="pb-2">From</th>
                <th class="pb-2">To</th>
                <th class="pb-2">Rate</th>
            </tr>
        </thead>
        <tbody>
            @foreach($taxBrackets as $bracket)
            <tr class="border-b">
                <td class="py-2">{{ number_format($bracket['min']) }}</td>
                <td class="py-2">{{ $bracket['max'] !== null ? number_format($bracket['max']) : 'Unlimited' }}</td>
                <td class="py-2">{{ $bracket['rate'] * 100 }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
