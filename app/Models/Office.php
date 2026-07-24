<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    use HasFactory;

    protected $table = 'offices';

    protected $fillable = [
        'name',
        'location',
        'latitude',
        'longitude',
        'opening_time',
        'closing_time',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'opening_time' => 'datetime',
        'closing_time' => 'datetime',
    ];

    public function getIsOpenAttribute()
    {
        $now = now();
        $opening = $this->opening_time;
        $closing = $this->closing_time;

        if (!$opening || !$closing) {
            return false;
        }

        return $now->between($opening, $closing);
    }
}
