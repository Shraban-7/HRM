<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = ['period_start', 'period_end', 'status', 'total_amount'];

    public function payslips(): HasMany
    {
        return $this->hasMany(Payslip::class);
    }
}
