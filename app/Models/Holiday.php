<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $table = 'holidays';

    protected $fillable = [
        'name',
        'date',
        'is_optional'
    ];

    protected $casts = [
        'date' => 'date',
        'is_optional' => 'boolean'
    ];
}