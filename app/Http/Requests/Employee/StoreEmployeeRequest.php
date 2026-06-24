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
            'department_id' => 'required|exists:departments,id',
            'employee_id' => 'required|string|unique:employees,employee_id',
            'hire_date' => 'required|date|before_or_equal:today',
            'designation' => 'required|string|max:255',
            'salary' => 'required|numeric|min:0|max:99999999.99',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email address is required.',
            'email.unique' => 'This email is already registered.',
            'username.required' => 'Username is required.',
            'username.unique' => 'This username is already taken.',
            'department_id.required' => 'Please select a department.',
            'employee_id.required' => 'Employee ID is required.',
            'employee_id.unique' => 'This employee ID already exists.',
            'hire_date.required' => 'Hire date is required.',
            'salary.required' => 'Salary amount is required.',
            'salary.min' => 'Salary must be a positive number.',
        ];
    }
}