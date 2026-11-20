<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExitRequest extends Model
{
    protected $fillable = ['employee_id', 'resignation_date', 'last_working_day', 'reason', 'status', 'interview_notes', 'approved_by'];

    protected $casts = [
        'resignation_date' => 'date',
        'last_working_day' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function clearances(): HasMany
    {
        return $this->hasMany(ExitClearance::class, 'exit_request_id');
    }
}
