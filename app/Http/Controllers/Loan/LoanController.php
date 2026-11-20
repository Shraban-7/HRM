<?php

namespace App\Http\Controllers\Loan;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Loan;
use App\Models\LoanRepayment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoanController extends Controller
{
    public function index(Request $request): View
    {
        $employees = Employee::with('user')->where('status', 'active')->get();
        $loans = Loan::with(['employee.user', 'approver', 'repayments'])->latest()->paginate(15);

        return view('loans.index', compact('loans', 'employees'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'loan_amount' => 'required|numeric|min:50',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'term_months' => 'required|integer|min:1|max:60',
        ]);

        $principal = (float) $validated['loan_amount'];
        $rate = (float) $validated['interest_rate'];
        $months = (int) $validated['term_months'];

        $totalInterest = $principal * ($rate / 100);
        $totalRepayable = $principal + $totalInterest;
        $monthlyInstallment = round($totalRepayable / $months, 2);

        Loan::create([
            'employee_id' => $validated['employee_id'],
            'loan_amount' => $principal,
            'interest_rate' => $rate,
            'term_months' => $months,
            'monthly_installment' => $monthlyInstallment,
            'total_repayable' => $totalRepayable,
            'remaining_balance' => $totalRepayable,
            'status' => 'pending',
        ]);

        return redirect()->route('loans.index')->with('success', 'Loan request registered.');
    }

    public function approve(Loan $loan): RedirectResponse
    {
        $loan->update([
            'status' => 'active',
            'approved_by' => Auth::id() ?? session('user_id'),
            'disbursed_at' => now(),
        ]);

        return redirect()->route('loans.index')->with('success', 'Loan approved and activated.');
    }

    public function recordPayment(Request $request, Loan $loan): RedirectResponse
    {
        $validated = $request->validate([
            'amount_paid' => 'required|numeric|min:0.01|max:'.$loan->remaining_balance,
            'repayment_date' => 'required|date',
            'payment_method' => 'required|string',
        ]);

        LoanRepayment::create([
            'loan_id' => $loan->id,
            'amount_paid' => $validated['amount_paid'],
            'repayment_date' => $validated['repayment_date'],
            'payment_method' => $validated['payment_method'],
        ]);

        $newBalance = max(0, $loan->remaining_balance - $validated['amount_paid']);
        $loan->update([
            'remaining_balance' => $newBalance,
            'status' => $newBalance <= 0 ? 'completed' : 'active',
        ]);

        return redirect()->route('loans.index')->with('success', 'Repayment logged successfully.');
    }
}
