<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|unique:users,username|min:3|max:255',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'marital_status' => 'nullable|string|max:20',
            'blood_group' => 'nullable|string|max:10',
            'address' => 'nullable|string|max:500',
            'employment_type' => 'nullable|in:full_time,part_time,contract,intern',
            'department_id' => 'required|exists:departments,id',
            'employee_id' => 'required|string|unique:employees,employee_id',
            'hire_date' => 'required|date|before_or_equal:today',
            'designation' => 'required|string|max:255',
            'salary' => 'required|numeric|min:0|max:99999999.99',
            'manager_id' => 'nullable|exists:employees,id',
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
            'email.required' => 'Email address is required.',
            'email.unique' => 'This email is already registered.',
            'username.required' => 'Username is required.',
            'username.unique' => 'This username is already taken.',
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'department_id.required' => 'Please select a department.',
            'employee_id.required' => 'Employee ID is required.',
            'employee_id.unique' => 'This employee ID already exists.',
            'hire_date.required' => 'Hire date is required.',
            'salary.required' => 'Salary amount is required.',
            'salary.min' => 'Salary must be a positive number.',
        ];
    }
}
