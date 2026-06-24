<?php

namespace App\Events;

use App\Models\Employee;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SalaryChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Employee $employee;
    public float $oldSalary;
    public float $newSalary;

    public function __construct(Employee $employee, float $oldSalary, float $newSalary)
    {
        $this->employee = $employee;
        $this->oldSalary = $oldSalary;
        $this->newSalary = $newSalary;
    }
}