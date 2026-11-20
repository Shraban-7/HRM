@extends('layouts.app')

@section('title', 'Expense Claims & Reimbursements')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Expense Claims & Reimbursements</h1>
            <p class="text-sm text-gray-500 mt-1">Submit travel, meal, and operational expense receipts with manager approvals.</p>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="document.getElementById('categoryModal').classList.remove('hidden')" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium text-sm rounded-lg transition">
                + New Category
            </button>
            <button onclick="document.getElementById('claimModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-lg shadow-sm transition flex items-center space-x-2">
                <i class="fas fa-receipt"></i>
                <span>Submit Expense Claim</span>
            </button>
        </div>
    </div>

    <!-- Expense Claims Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50/50">
            <h2 class="text-base font-bold text-gray-800">Submitted Expense Claims</h2>
            <div class="flex items-center space-x-2">
                <a href="{{ route('expenses.index') }}" class="text-xs px-2.5 py-1 rounded {{ !request('status') ? 'bg-indigo-600 text-white font-bold' : 'text-gray-600 hover:bg-gray-100' }}">All</a>
                <a href="{{ route('expenses.index', ['status' => 'pending']) }}" class="text-xs px-2.5 py-1 rounded {{ request('status') === 'pending' ? 'bg-amber-500 text-white font-bold' : 'text-gray-600 hover:bg-gray-100' }}">Pending</a>
                <a href="{{ route('expenses.index', ['status' => 'approved']) }}" class="text-xs px-2.5 py-1 rounded {{ request('status') === 'approved' ? 'bg-green-600 text-white font-bold' : 'text-gray-600 hover:bg-gray-100' }}">Approved</a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3">Employee</th>
                        <th class="px-6 py-3">Category</th>
                        <th class="px-6 py-3">Claim Date</th>
                        <th class="px-6 py-3">Amount</th>
                        <th class="px-6 py-3">Receipt</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Approval Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($expenses as $claim)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">{{ $claim->employee->user->username ?? 'Employee #'.$claim->employee_id }}</div>
                                <div class="text-xs text-gray-500">{{ $claim->description ?? 'No description provided' }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs font-semibold text-gray-700">
                                {{ $claim->category->name }}
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-600">
                                {{ $claim->claim_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm font-extrabold text-gray-900">
                                ${{ number_format($claim->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 text-xs">
                                @if($claim->receipt_path)
                                    <a href="/storage/{{ $claim->receipt_path }}" target="_blank" class="text-indigo-600 hover:underline inline-flex items-center space-x-1 font-semibold">
                                        <i class="fas fa-paperclip"></i>
                                        <span>View Receipt</span>
                                    </a>
                                @else
                                    <span class="text-gray-400 italic">None</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($claim->status === 'approved')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">Approved</span>
                                @elseif($claim->status === 'reimbursed')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800">Reimbursed</span>
                                @elseif($claim->status === 'rejected')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">Rejected</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">Pending Review</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                @if($claim->status === 'pending')
                                    <form action="{{ route('expenses.approve', $claim) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-xs font-bold transition">
                                            Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('expenses.reject', $claim) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs font-bold transition">
                                            Reject
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400">Processed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-400">No expense claims found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $expenses->links() }}
        </div>
    </div>
</div>

<!-- Submit Claim Modal -->
<div id="claimModal" class="fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Submit Expense Claim</h3>
        <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
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
                <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Expense Category</label>
                <select name="expense_category_id" class="w-full px-3 py-2 border rounded-lg text-sm" required>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }} (Max: ${{ number_format($cat->max_limit, 0) }})</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Amount ($)</label>
                    <input type="number" step="0.01" name="amount" required placeholder="149.50" class="w-full px-3 py-2 border rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Claim Date</label>
                    <input type="date" name="claim_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border rounded-lg text-sm">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Description / Business Purpose</label>
                <textarea name="description" rows="2" class="w-full px-3 py-2 border rounded-lg text-sm" placeholder="Client lunch with Acme Corp"></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Receipt Attachment (PDF/Image)</label>
                <input type="file" name="receipt" class="w-full px-3 py-1.5 border rounded-lg text-sm">
            </div>
            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="document.getElementById('claimModal').classList.add('hidden')" class="px-4 py-2 border rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-bold hover:bg-indigo-700">Submit Claim</button>
            </div>
        </form>
    </div>
</div>

<!-- Category Modal -->
<div id="categoryModal" class="fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-xl">
        <h3 class="text-lg font-bold text-gray-900 mb-4">New Expense Category</h3>
        <form action="{{ route('expenses.categories.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Category Name</label>
                <input type="text" name="name" required placeholder="Travel, Meals, Office Supplies" class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Code</label>
                <input type="text" name="code" required placeholder="TRV, MEAL, OFF" class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Max Claim Limit ($)</label>
                <input type="number" step="0.01" name="max_limit" value="1000.00" required class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>
            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="document.getElementById('categoryModal').classList.add('hidden')" class="px-4 py-2 border rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-bold hover:bg-indigo-700">Create</button>
            </div>
        </form>
    </div>
</div>
@endsection
