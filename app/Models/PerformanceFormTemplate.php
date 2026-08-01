<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PerformanceFormTemplate extends Model
{
    use HasFactory;

    protected $table = 'performance_form_templates';

    protected $fillable = ['name', 'slug', 'description', 'questions', 'is_active'];

    protected $casts = [
        'questions' => 'array',
        'is_active' => 'boolean',
    ];

    public function reviews(): HasMany
    {
        return $this->hasMany(PerformanceReview::class, 'form_template_id');
    }
}
