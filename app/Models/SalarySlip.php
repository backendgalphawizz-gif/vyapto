<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalarySlip extends Model
{
    use HasFactory;

    protected $primaryKey = 'slip_id';

    protected $fillable = [
        'employee_id',
        'month',
        'year',
        'file_path',
        'basic_salary',
        // PT
        'pt_value', 'pt_type',
        // HRA
        'hra_value', 'hra_type',
        // Special Allowance
        'special_allow_value', 'special_allow_type',
        // Statutory Bonus
        'stat_bonus_value', 'stat_bonus_type',
        // Add: Perquisite and Other Income
        'perquisite_value', 'perquisite_type',
        // Less: Exempt Reimbursement
        'exempt_reimburse_value', 'exempt_reimburse_type',
        // Less: Deduction U/s 10
        'deduction_10_value', 'deduction_10_type',
        // Less: Deduction U/s 16 (Std. Deduction)
        'deduction_16_value', 'deduction_16_type',
        // Less: Deduction U/s 24 (Housing Loss)
        'deduction_24_value', 'deduction_24_type',
        // Less: Deduction U/s Chapter VIA
        'deduction_via_value', 'deduction_via_type',
        // Computed totals
        'net_taxable_income',
        'total_tax_payable',
        'total_tax_recovered',
        'balance_tax_recoverable',
    ];

    protected $casts = [
        'basic_salary'           => 'decimal:2',
        'pt_value'               => 'decimal:2',
        'hra_value'              => 'decimal:2',
        'special_allow_value'    => 'decimal:2',
        'stat_bonus_value'       => 'decimal:2',
        'perquisite_value'       => 'decimal:2',
        'exempt_reimburse_value' => 'decimal:2',
        'deduction_10_value'     => 'decimal:2',
        'deduction_16_value'     => 'decimal:2',
        'deduction_24_value'     => 'decimal:2',
        'deduction_via_value'    => 'decimal:2',
        'net_taxable_income'     => 'decimal:2',
        'total_tax_payable'      => 'decimal:2',
        'total_tax_recovered'    => 'decimal:2',
        'balance_tax_recoverable'=> 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
