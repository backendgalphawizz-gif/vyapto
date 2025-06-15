<?php

// app/Models/AnnouncementLog.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnouncementLog extends Model
{
    protected $fillable = [
        'announcement_id',
        'learner_id',
        'is_sent',
        'sent_at',
    ];

    public function announcement()
    {
        return $this->belongsTo(Announcement::class);
    }

    public function learner()
    {
        return $this->belongsTo(Learner::class);
    }

}
