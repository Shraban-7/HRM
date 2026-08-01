<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'marital_status' => 'nullable|string|max:20',
            'blood_group' => 'nullable|string|max:10',
            'address' => 'nullable|string|max:500',
            'employment_type' => 'nullable|in:full_time,part_time,contract,intern',
            'manager_id' => ['nullable', Rule::exists('employees', 'id')],
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'bank_name' => 'nullable|string|max:100',
            'account_name' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:50',
            'routing_number' => 'nullable|string|max:50',
            'swift_code' => 'nullable|string|max:20',
            'tax_id' => 'nullable|string|max:50',
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
