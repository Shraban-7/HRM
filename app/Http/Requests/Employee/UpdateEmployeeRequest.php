<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'department_id' => 'required|exists:departments,id',
            'designation' => 'required|string|max:255',
            'salary' => 'required|numeric|min:0|max:99999999.99',
            'status' => 'required|in:active,inactive,terminated',
        ];
    }

    public function messages(): array
    {
        return [
            'department_id.required' => 'Please select a department.',
            'department_id.exists' => 'Selected department does not exist.',
            'designation.required' => 'Designation is required.',
            'salary.required' => 'Salary amount is required.',
            'salary.min' => 'Salary must be a positive number.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be one of: active, inactive, terminated.',
        ];
    }
}