<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Goal extends Model
{
    protected $fillable = ['employee_id', 'title', 'description', 'target_date', 'status'];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}