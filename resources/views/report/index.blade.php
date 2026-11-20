@extends('layouts.app')

@section('title', 'Executive Reports & Data Export')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Executive Reporting & Data Hub</h1>
            <p class="text-sm text-gray-500 mt-1">Export universal CSV datasets, import bulk employee rosters, and run background report jobs.</p>
        </div>
    </div>

    <!-- 1-Click Universal CSV Data Export Center -->
    <div>
        <h2 class="text-sm font-bold uppercase tracking-wider text-gray-700 mb-4 flex items-center space-x-2">
            <i class="fas fa-file-csv text-emerald-600 text-base"></i>
            <span>Instant Data Export (CSV / Excel Compatible)</span>
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Employee Export -->
            <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg mb-3">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="text-base font-bold text-gray-900">Workforce Master Roster</h3>
                    <p class="text-xs text-gray-500 mt-1">All employee identifiers, job titles, departments, hire dates, and compensation tiers.</p>
                </div>
                <div class="mt-6 pt-4 border-t border-gray-100">
                    <a href="{{ route('reports.export.employees') }}" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold transition flex items-center justify-center space-x-1.5 shadow-sm">
                        <i class="fas fa-download"></i>
                        <span>Download Employee CSV</span>
                    </a>
                </div>
            </div>

            <!-- Payroll Export -->
            <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg mb-3">
                        <i class="fas fa-money-check-dollar"></i>
                    </div>
                    <h3 class="text-base font-bold text-gray-900">Payroll Disbursement Ledger</h3>
                    <p class="text-xs text-gray-500 mt-1">Itemized payslip breakdown (gross pay, tax deductions, net salaries) by billing cycle.</p>
                </div>
                <form action="{{ route('reports.export.payroll') }}" method="GET" class="mt-4 pt-3 border-t border-gray-100 space-y-2">
                    <select name="payroll_id" class="w-full px-2.5 py-1.5 border rounded-lg text-xs">
                        @foreach($payrolls as $p)
                            <option value="{{ $p->id }}">Cycle #{{ $p->id }} ({{ \Carbon\Carbon::parse($p->period_start)->format('M d') }} - {{ \Carbon\Carbon::parse($p->period_end)->format('M d, Y') }})</option>
                        @endforeach
                    </select>
                    <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition flex items-center justify-center space-x-1.5 shadow-sm">
                        <i class="fas fa-download"></i>
                        <span>Download Payroll CSV</span>
                    </button>
                </form>
            </div>

            <!-- Attendance Export -->
            <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg mb-3">
                        <i class="fas fa-fingerprint"></i>
                    </div>
                    <h3 class="text-base font-bold text-gray-900">Attendance Clock Ledger</h3>
                    <p class="text-xs text-gray-500 mt-1">Daily clock-in and clock-out logs, overtime records, and punctual/late presence logs.</p>
                </div>
                <form action="{{ route('reports.export.attendance') }}" method="GET" class="mt-4 pt-3 border-t border-gray-100 space-y-2">
                    <input type="month" name="month" value="{{ date('Y-m') }}" class="w-full px-2.5 py-1.5 border rounded-lg text-xs">
                    <button type="submit" class="w-full py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-bold transition flex items-center justify-center space-x-1.5 shadow-sm">
                        <i class="fas fa-download"></i>
                        <span>Download Attendance CSV</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Bulk Employee Import Panel -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-base font-bold text-gray-900 flex items-center space-x-2 mb-2">
                <i class="fas fa-file-import text-indigo-600"></i>
                <span>Bulk Employee CSV Import</span>
            </h2>
            <p class="text-xs text-gray-500 mb-4">Quickly onboard dozens or hundreds of employees at once using a comma-separated values (CSV) spreadsheet.</p>

            <form action="{{ route('reports.import.employees') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Select CSV File</label>
                    <input type="file" name="csv_file" accept=".csv" required class="w-full px-3 py-2 border rounded-lg text-xs">
                </div>
                <div class="bg-gray-50 p-3 rounded-lg border border-gray-100 text-[11px] text-gray-600">
                    <div class="font-bold mb-1">Expected CSV Column Header Order:</div>
                    <code class="text-indigo-600 bg-white px-1.5 py-0.5 rounded border border-gray-200">username, email, department_id, employee_id, hire_date, designation, salary</code>
                </div>
                <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-lg transition shadow-sm">
                    Upload & Process Roster Import
                </button>
            </form>
        </div>

        <!-- Asynchronous Background Report Generation -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-base font-bold text-gray-900 flex items-center space-x-2 mb-2">
                <i class="fas fa-cogs text-purple-600"></i>
                <span>Asynchronous Report Builder</span>
            </h2>
            <p class="text-xs text-gray-500 mb-4">Queue large aggregated analytical reports for background processing.</p>

            <form method="POST" action="{{ route('reports.generate') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Report Type</label>
                    <select name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs" required>
                        @foreach($reportTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Period Start</label>
                        <input type="date" name="period_start" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Period End</label>
                        <input type="date" name="period_end" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs">
                    </div>
                </div>
                <button type="submit" class="w-full py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs rounded-lg transition shadow-sm">
                    Queue Background Generation
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
