<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeBankDetail extends Model
{
    protected $fillable = [
        'employee_id', 'bank_name', 'account_name', 'account_number',
        'routing_number', 'swift_code', 'tax_id',
    ];

    protected $casts = [
        'account_number' => 'encrypted',
        'routing_number' => 'encrypted',
        'swift_code' => 'encrypted',
        'tax_id' => 'encrypted',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
