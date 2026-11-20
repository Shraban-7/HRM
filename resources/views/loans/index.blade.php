@extends('layouts.app')

@section('title', 'Employee Loans & Salary Advances')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Employee Loans & Advances</h1>
            <p class="text-sm text-gray-500 mt-1">Manage corporate loan disbursements, interest schedules, and automatic monthly payroll EMI deductions.</p>
        </div>
        <button onclick="document.getElementById('loanModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-lg shadow-sm transition flex items-center space-x-2">
            <i class="fas fa-hand-holding-usd"></i>
            <span>Register Loan Request</span>
        </button>
    </div>

    <!-- Loans Ledger Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50/50">
            <h2 class="text-base font-bold text-gray-800">Active & Historical Loans</h2>
            <span class="text-xs text-gray-500">{{ $loans->total() }} recorded</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3">Employee</th>
                        <th class="px-6 py-3">Principal Amount</th>
                        <th class="px-6 py-3">Tenure / Rate</th>
                        <th class="px-6 py-3">Monthly EMI</th>
                        <th class="px-6 py-3">Remaining Balance</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($loans as $loan)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">{{ $loan->employee->user->username ?? 'Employee #'.$loan->employee_id }}</div>
                                <div class="text-xs text-gray-500">{{ $loan->employee->employee_id }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm font-extrabold text-gray-900">
                                ${{ number_format($loan->loan_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-600">
                                <div>{{ $loan->term_months }} Months</div>
                                <div class="text-gray-400">{{ $loan->interest_rate }}% Annual Interest</div>
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-indigo-700">
                                ${{ number_format($loan->monthly_installment, 2) }} / mo
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <span class="font-bold {{ $loan->remaining_balance > 0 ? 'text-rose-600' : 'text-green-600' }}">
                                    ${{ number_format($loan->remaining_balance, 2) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($loan->status === 'active')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">Active Repaying</span>
                                @elseif($loan->status === 'completed')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">Fully Repaid</span>
                                @elseif($loan->status === 'pending')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Pending Approval</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700">{{ ucfirst($loan->status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                @if($loan->status === 'pending')
                                    <form action="{{ route('loans.approve', $loan) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-xs font-bold transition">
                                            Approve & Disburse
                                        </button>
                                    </form>
                                @elseif($loan->status === 'active')
                                    <button onclick="openRepayModal({{ $loan->id }}, '{{ number_format($loan->monthly_installment, 2) }}', '{{ number_format($loan->remaining_balance, 2) }}')" class="px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-xs font-bold transition">
                                        Manual Repayment
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-400">No loans registered yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $loans->links() }}
        </div>
    </div>
</div>

<!-- Register Loan Modal -->
<div id="loanModal" class="fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Register Employee Loan</h3>
        <form action="{{ route('loans.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Employee</label>
                <select name="employee_id" class="w-full px-3 py-2 border rounded-lg text-sm" required>
                    <option value="">Select Employee...</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->user->username ?? 'ID #'.$emp->id }} ({{ $emp->employee_id }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Principal Amount ($)</label>
                <input type="number" step="0.01" name="loan_amount" required placeholder="5000.00" class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Interest Rate (%)</label>
                    <input type="number" step="0.1" name="interest_rate" value="5.0" required class="w-full px-3 py-2 border rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Tenure (Months)</label>
                    <input type="number" name="term_months" value="12" required class="w-full px-3 py-2 border rounded-lg text-sm">
                </div>
            </div>
            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="document.getElementById('loanModal').classList.add('hidden')" class="px-4 py-2 border rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-bold hover:bg-indigo-700">Calculate & Register</button>
            </div>
        </form>
    </div>
</div>

<!-- Manual Repayment Modal -->
<div id="repayModal" class="fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-xl">
        <h3 class="text-lg font-bold text-gray-900 mb-1">Record Repayment</h3>
        <p id="repayModalSubtitle" class="text-xs text-gray-500 mb-4"></p>
        <form id="repayForm" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Repayment Amount ($)</label>
                <input type="number" step="0.01" id="repayAmount" name="amount_paid" required class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Payment Date</label>
                <input type="date" name="repayment_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Method</label>
                <select name="payment_method" class="w-full px-3 py-2 border rounded-lg text-sm">
                    <option value="bank_transfer">Bank Wire Transfer</option>
                    <option value="cash">Direct Cash</option>
                    <option value="payroll_deduction">Manual Payroll Deduction</option>
                </select>
            </div>
            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="document.getElementById('repayModal').classList.add('hidden')" class="px-4 py-2 border rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-bold hover:bg-indigo-700">Log Payment</button>
            </div>
        </form>
    </div>
</div>

<script>
function openRepayModal(id, installment, balance) {
    document.getElementById('repayModalSubtitle').innerText = 'Monthly EMI: $' + installment + ' | Remaining: $' + balance;
    document.getElementById('repayAmount').value = installment;
    document.getElementById('repayForm').action = '/loans/' + id + '/repay';
    document.getElementById('repayModal').classList.remove('hidden');
}
</script>
@endsection
