<?php
// app/Models/Hub.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hub extends Model
{
    use HasFactory;

    protected $table = 'hubs'; 

    protected $fillable = [
        'name',
        'location',
        'latitude',
        'longitude',
        'opening_time',
        'closing_time'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'opening_time' => 'datetime',
        'closing_time' => 'datetime',
    ];

    // Accessor to check if hub is currently open (time-of-day only)
    public function getIsOpenAttribute()
    {
        if (!$this->opening_time || !$this->closing_time) {
            return false;
        }

        $now = now()->format('H:i:s');
        $opening = \Carbon\Carbon::parse($this->opening_time)->format('H:i:s');
        $closing = \Carbon\Carbon::parse($this->closing_time)->format('H:i:s');

        if ($opening <= $closing) {
            return $now >= $opening && $now <= $closing;
        }

        // Overnight window (e.g. 22:00 → 06:00)
        return $now >= $opening || $now <= $closing;
    }
}