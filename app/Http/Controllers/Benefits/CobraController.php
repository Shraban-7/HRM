<?php

namespace App\Http\Controllers\Benefits;

use App\Http\Controllers\Controller;
use App\Models\BenefitEnrollment;
use App\Models\CobraCoverage;
use App\Services\Benefits\CobraService;
use Illuminate\Http\Request;

class CobraController extends Controller
{
    public function __construct(protected CobraService $cobraService) {}

    public function index()
    {
        $coverages = CobraCoverage::with(['employee.user', 'benefit'])
            ->orderByRaw("CASE status WHEN 'active' THEN 0 WHEN 'expired' THEN 1 ELSE 2 END")
            ->orderBy('coverage_end_date', 'desc')
            ->paginate(15);

        $this->cobraService->expireOverdue();

        return view('benefits.cobra', compact('coverages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'benefit_id' => 'required|exists:benefits,id',
            'qualifying_event_date' => 'required|date',
            'coverage_start_date' => 'required|date',
            'coverage_end_date' => 'required|date|after:coverage_start_date',
            'premium_amount' => 'nullable|numeric|min:0',
        ]);

        $this->cobraService->createFromEnrollment(
            BenefitEnrollment::firstOrCreate([
                'employee_id' => $request->employee_id,
                'benefit_id' => $request->benefit_id,
            ], [
                'status' => 'cancelled',
                'enrolled_at' => now(),
            ])
        );

        return redirect()->route('cobra.index')->with('success', 'COBRA coverage created.');
    }

    public function terminate(CobraCoverage $coverage)
    {
        $this->cobraService->terminate($coverage);

        return redirect()->route('cobra.index')->with('success', 'COBRA coverage terminated.');
    }
}
