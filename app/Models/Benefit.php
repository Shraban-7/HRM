<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Benefit extends Model
{
    protected $fillable = ['name', 'description', 'cost', 'type', 'is_active'];

    public function enrollments(): HasMany
    {
        return $this->hasMany(BenefitEnrollment::class);
    }
}