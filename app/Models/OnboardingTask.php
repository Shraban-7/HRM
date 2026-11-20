<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OnboardingTask extends Model
{
    protected $fillable = ['checklist_id', 'title', 'description', 'assigned_role', 'is_required'];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(OnboardingChecklist::class, 'checklist_id');
    }

    public function progress(): HasMany
    {
        return $this->hasMany(EmployeeOnboardingProgress::class, 'task_id');
    }
}
