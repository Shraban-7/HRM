<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CobraCoverage extends Model
{
    use HasFactory;

    protected $table = 'cobra_coverages';

    protected $fillable = [
        'employee_id',
        'benefit_id',
        'benefit_enrollment_id',
        'qualifying_event_date',
        'coverage_start_date',
        'coverage_end_date',
        'status',
        'premium_amount',
    ];

    protected $casts = [
        'qualifying_event_date' => 'date',
        'coverage_start_date' => 'date',
        'coverage_end_date' => 'date',
        'premium_amount' => 'float',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function benefit(): BelongsTo
    {
        return $this->belongsTo(Benefit::class);
    }
}
