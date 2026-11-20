<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExitClearance extends Model
{
    protected $fillable = ['exit_request_id', 'department', 'cleared_by', 'status', 'remarks', 'cleared_at'];

    protected $casts = [
        'cleared_at' => 'datetime',
    ];

    public function exitRequest(): BelongsTo
    {
        return $this->belongsTo(ExitRequest::class, 'exit_request_id');
    }

    public function clearedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cleared_by');
    }
}
