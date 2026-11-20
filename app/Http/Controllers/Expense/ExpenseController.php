<?php

namespace App\Http\Controllers\Expense;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        $categories = ExpenseCategory::all();
        $employees = Employee::with('user')->where('status', 'active')->get();

        $query = Expense::with(['employee.user', 'category', 'approver']);
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $expenses = $query->latest()->paginate(15);

        return view('expenses.index', compact('expenses', 'categories', 'employees'));
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'code' => 'required|string|max:20|unique:expense_categories,code',
            'max_limit' => 'required|numeric|min:1',
        ]);

        ExpenseCategory::create($validated);

        return redirect()->route('expenses.index')->with('success', 'Expense category created.');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'claim_date' => 'required|date|before_or_equal:today',
            'description' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('receipt')) {
            $validated['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
        }

        unset($validated['receipt']);
        Expense::create($validated);

        return redirect()->route('expenses.index')->with('success', 'Expense claim submitted for approval.');
    }

    public function approve(Request $request, Expense $expense): RedirectResponse
    {
        $expense->update([
            'status' => 'approved',
            'approved_by' => Auth::id() ?? session('user_id'),
        ]);

        return redirect()->route('expenses.index')->with('success', 'Expense claim approved.');
    }

    public function reject(Request $request, Expense $expense): RedirectResponse
    {
        $expense->update(['status' => 'rejected']);

        return redirect()->route('expenses.index')->with('success', 'Expense claim rejected.');
    }
}
