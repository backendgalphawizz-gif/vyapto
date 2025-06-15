<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearnerAttendance extends Model
{
     use HasFactory;

    protected $table = 'learner_attendance';

    protected $fillable = [
        'learner_id',
        'date',
        'am_in',
        'am_out',
        'pm_in',
        'pm_out',
    ];

    public function learner()
    {
        return $this->belongsTo(Learner::class);
    }
}
