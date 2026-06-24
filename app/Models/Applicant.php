<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Applicant extends Model
{
    protected $fillable = ['job_post_id', 'name', 'email', 'phone', 'resume_path', 'status'];

    public function jobPost(): BelongsTo
    {
        return $this->belongsTo(JobPost::class);
    }

    public function interview(): HasOne
    {
        return $this->hasOne(Interview::class);
    }
}