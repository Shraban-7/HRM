<?php

namespace App\Http\Requests\Payroll;

use Illuminate\Foundation\Http\FormRequest;

class StorePayrollRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'period_start' => 'required|date',
            'period_end' => 'required|date|after:period_start',
        ];
    }

    public function messages(): array
    {
        return [
            'period_start.required' => 'Period start date is required.',
            'period_start.date' => 'Period start must be a valid date.',
            'period_end.required' => 'Period end date is required.',
            'period_end.date' => 'Period end must be a valid date.',
            'period_end.after' => 'Period end must be after the start date.',
        ];
    }
}