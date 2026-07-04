<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;

class PunchIn extends Model
{
    protected $table = 'attendance';

    protected $fillable = [
        'id',
        'employee_id',
		'punch_in_date',
        'punch_in_time',
        'punch_out_time',
		'punch_in_lat',
		'punch_in_long',
		'punch_in_location',
		'punch_in_exception',
		'punch_out_date',
		'punch_out_lat',
		'punch_out_long',
		'punch_out_location',
		'punch_out_exception',
		'punch_in_image'
    ];
}