<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = [
        'type',
        'value'
    ];
}