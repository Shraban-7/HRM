@extends('layouts.app')
@section('title', 'Payroll')
@section('content')
<div class="bg-white p-6 rounded-lg shadow">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Payroll Periods</h3>
        <a href="{{ route('payrolls.create') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"><i class="fas fa-plus mr-2"></i>New Period</a>
    </div>
    <table class="w-full datatable">
        <thead><tr><th>ID</th><th>Period</th><th>Status</th><th>Total</th><th>Actions</th></tr></thead>
        <tbody>
            @foreach($payrolls as $p)
            <tr>
                <td>{{ $p->id }}</td>
                <td>{{ $p->period_start }} to {{ $p->period_end }}</td>
                <td><span class="px-2 py-1 rounded text-sm bg-{{ $p->status === 'completed' ? 'green' : ($p->status === 'running' ? 'yellow' : 'gray') }}-100 text-{{ $p->status === 'completed' ? 'green' : ($p->status === 'running' ? 'yellow' : 'gray') }}-800">{{ $p->status }}</span></td>
                <td>${{ number_format($p->total_amount, 2) }}</td>
                <td><a href="{{ route('payrolls.show', $p) }}" class="text-blue-600 hover:underline"><i class="fas fa-eye"></i></a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection