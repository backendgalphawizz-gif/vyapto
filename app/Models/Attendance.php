<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Model;



class Attendance extends Model
{
    protected $table = 'attendance';

    protected $fillable = [
        'employee_id',
        'punch_in_date',
        'punch_in_time',
        'punch_out_time',
        'punch_in_lat',
        'punch_in_long',
        'punch_in_location',
        'punch_in_exception',
        'punch_in_image',
        'punch_out_date',
        'punch_out_lat',
        'punch_out_long',
        'punch_out_location',
        'punch_out_exception',
        'punch_out_image'
    ];

    protected $casts = [
        'punch_in_time'  => 'datetime',
        'punch_out_time' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}

