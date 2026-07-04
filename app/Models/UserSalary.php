<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSalary extends Model
{
    use HasFactory;

    protected $table = 'user_salaries';

    protected $fillable = [
        'user_id',
        'salary_amount',
        'salary_type',
        'effective_from',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'salary_amount'  => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function salaryTypeLabels(): array
    {
        return [
            'monthly' => 'Monthly',
            'weekly'  => 'Weekly',
            'daily'   => 'Daily',
        ];
    }
}
