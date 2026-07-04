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

    // Accessor to check if hub is currently open
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