<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompanyPolicy extends Model
{
    protected $fillable = ['title', 'description', 'file_path', 'requires_acknowledgement', 'published_at'];

    protected $casts = [
        'requires_acknowledgement' => 'boolean',
        'published_at' => 'date',
    ];

    public function acknowledgements(): HasMany
    {
        return $this->hasMany(PolicyAcknowledgement::class);
    }
}
