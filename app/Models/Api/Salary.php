<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    protected $table = 'employee_salary';

    protected $fillable = [
        'id',
        'employee_id',
		'date',
        'basic_salary',
        'gross_salary',
		'net_salary',
		'total_salary',
		'status',
    ];
}