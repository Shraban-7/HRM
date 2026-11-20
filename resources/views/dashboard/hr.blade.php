@extends('layouts.app')

@section('title', 'Executive HR & Operations Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Top Welcome & Controls -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">Workforce & Operations Analytics</h1>
            <p class="text-xs text-gray-500 mt-1">Real-time organizational headcount, progressive payroll velocity, and attendance tracking.</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('employees.create') }}" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold shadow-sm transition flex items-center space-x-1.5">
                <i class="fas fa-user-plus text-[10px]"></i>
                <span>Add Employee</span>
            </a>
            <a href="{{ route('payrolls.create') }}" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold shadow-sm transition flex items-center space-x-1.5">
                <i class="fas fa-play text-[10px]"></i>
                <span>Run Payroll</span>
            </a>
        </div>
    </div>

    <!-- Metric KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Workforce</span>
                <div class="text-3xl font-extrabold text-gray-900 mt-1">{{ number_format($totalEmployees) }}</div>
                <div class="text-[11px] text-emerald-600 font-semibold mt-1 flex items-center space-x-1">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ $activeEmployees }} active profiles</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
                <i class="fas fa-users"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Departments</span>
                <div class="text-3xl font-extrabold text-gray-900 mt-1">{{ $totalDepartments }}</div>
                <div class="text-[11px] text-indigo-600 font-semibold mt-1">
                    <a href="{{ route('org-chart') }}" class="hover:underline">View visual org tree &rarr;</a>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl">
                <i class="fas fa-sitemap"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Pending Approvals</span>
                <div class="text-3xl font-extrabold text-amber-600 mt-1">{{ $pendingLeaves }}</div>
                <div class="text-[11px] text-amber-600 font-semibold mt-1">
                    <a href="{{ route('leave.index') }}" class="hover:underline">Review leave requests &rarr;</a>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                <i class="fas fa-calendar-alt"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Payroll Cycle</span>
                <div class="text-3xl font-extrabold text-gray-900 mt-1">{{ $activePayrolls }} Active</div>
                <div class="text-[11px] text-emerald-600 font-semibold mt-1 flex items-center space-x-1">
                    <i class="fas fa-shield-alt"></i>
                    <span>Tax engine synchronized</span>
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fas fa-money-check-alt"></i>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Monthly Payroll Bar Chart -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Monthly Compensation Expenditure</h2>
                    <p class="text-xs text-gray-400">Total gross payroll disbursements across billing cycles</p>
                </div>
                <span class="text-xs bg-indigo-50 text-indigo-700 font-semibold px-2.5 py-1 rounded-md">Currency: USD ($)</span>
            </div>
            <div class="h-64">
                <canvas id="payrollChart"></canvas>
            </div>
        </div>

        <!-- Department Headcount Doughnut Chart -->
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Workforce by Dept</h2>
                    <p class="text-xs text-gray-400">Headcount distribution</p>
                </div>
            </div>
            <div class="h-64 flex items-center justify-center">
                <canvas id="deptChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Bottom Row: Recent Hires & Quick Navigation -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Employee Hires -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50 flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-800">Recent Employee Hires</h3>
                <a href="{{ route('employees.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">View Full Directory &rarr;</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($recentHires as $hire)
                    <div class="px-6 py-3.5 flex items-center justify-between hover:bg-gray-50 text-xs">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs">
                                {{ strtoupper(substr($hire->user->username ?? 'E', 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-bold text-gray-900">{{ $hire->user->username ?? 'Employee #'.$hire->id }}</div>
                                <div class="text-gray-400">{{ $hire->designation }} &bull; {{ $hire->department->name ?? 'General' }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-green-50 text-green-700 border border-green-200">Active</span>
                            <div class="text-[10px] text-gray-400 mt-0.5">{{ $hire->employee_id }}</div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-400 text-xs">No employee records yet.</div>
                @endforelse
            </div>
        </div>

        <!-- Quick Platform Actions -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center space-x-2">
                    <i class="fas fa-bolt text-amber-500"></i>
                    <span>Quick Administration</span>
                </h3>
                <div class="grid grid-cols-2 gap-3 text-xs">
                    <a href="{{ route('jobs.create') }}" class="p-3 rounded-xl border border-gray-200 hover:border-indigo-400 hover:bg-indigo-50/30 transition text-center font-semibold text-gray-700">
                        <i class="fas fa-briefcase text-indigo-600 block text-lg mb-1.5"></i>
                        Post Job Vacancy
                    </a>
                    <a href="{{ route('assets.index') }}" class="p-3 rounded-xl border border-gray-200 hover:border-indigo-400 hover:bg-indigo-50/30 transition text-center font-semibold text-gray-700">
                        <i class="fas fa-laptop text-emerald-600 block text-lg mb-1.5"></i>
                        Hardware Assets
                    </a>
                    <a href="{{ route('expenses.index') }}" class="p-3 rounded-xl border border-gray-200 hover:border-indigo-400 hover:bg-indigo-50/30 transition text-center font-semibold text-gray-700">
                        <i class="fas fa-receipt text-blue-600 block text-lg mb-1.5"></i>
                        Expense Claims
                    </a>
                    <a href="{{ route('reports.index') }}" class="p-3 rounded-xl border border-gray-200 hover:border-indigo-400 hover:bg-indigo-50/30 transition text-center font-semibold text-gray-700">
                        <i class="fas fa-chart-pie text-purple-600 block text-lg mb-1.5"></i>
                        Export Reports
                    </a>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100 text-center">
                <a href="{{ route('settings.index') }}" class="text-xs font-semibold text-gray-500 hover:text-gray-900 flex items-center justify-center space-x-1">
                    <i class="fas fa-sliders-h"></i>
                    <span>Open Company Branding Settings</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Payroll Expenditure Bar Chart
    const payrollCtx = document.getElementById('payrollChart');
    if (payrollCtx) {
        new Chart(payrollCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($payrollLabels) !!},
                datasets: [{
                    label: 'Gross Payroll ($)',
                    data: {!! json_encode($payrollAmounts) !!},
                    backgroundColor: 'rgba(99, 102, 241, 0.85)',
                    borderColor: 'rgb(79, 70, 229)',
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) { return '$' + value.toLocaleString(); },
                            font: { size: 10 }
                        },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: {
                        ticks: { font: { size: 10 } },
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) { return 'Payroll: $' + ctx.raw.toLocaleString(); }
                        }
                    }
                }
            }
        });
    }

    // Department Doughnut Chart
    const deptCtx = document.getElementById('deptChart');
    if (deptCtx) {
        new Chart(deptCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($deptLabels) !!},
                datasets: [{
                    data: {!! json_encode($deptCounts) !!},
                    backgroundColor: [
                        '#6366f1', '#10b981', '#f59e0b', '#06b6d4', '#ec4899', '#8b5cf6'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, font: { size: 10 } }
                    }
                },
                cutout: '65%'
            }
        });
    }
});
</script>
@endsection
