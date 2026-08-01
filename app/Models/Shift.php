<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'start_time', 'end_time', 'color', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
