<?php

namespace Database\Factories;

use App\Models\Payslip;
use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Database\Eloquent\Factories\Factory;

class PayslipFactory extends Factory
{
    protected $model = Payslip::class;

    public function definition(): array
    {
        $gross = $this->faker->numberBetween(3000, 15000);
        $deductions = round($gross * 0.1, 2);

        return [
            'employee_id' => Employee::factory(),
            'payroll_id' => Payroll::factory(),
            'gross' => $gross,
            'deductions' => $deductions,
            'net' => $gross - $deductions,
            'issued_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'pdf_path' => null,
        ];
    }
}