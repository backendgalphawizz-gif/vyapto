<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class EmailLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'email',
        'subject',
        'sent_at',
    ];

    /**
     * Cast sent_at to a Carbon instance.
     */
    protected $casts = [
        'sent_at' => 'datetime',
    ];

    /**
     * Each log entry belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
