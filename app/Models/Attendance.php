<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    public $timestamps = false;
    protected $fillable = ['employee_id', 'date', 'clock_in', 'clock_out', 'status'];
    protected $primaryKey = null;
    public $incrementing = false;

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}