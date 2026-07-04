<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSalary extends Model
{
    use HasFactory;

    protected $table = 'employee_salary';

    protected $fillable = [
        'employee_id',
        'date',
        'basic_salary',
        'gross_salary',
        'net_salary',
        'total_salary',
        'status',
    ];

    protected $casts = [
        'date'         => 'date',
        'basic_salary' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'net_salary'   => 'decimal:2',
        'total_salary' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
