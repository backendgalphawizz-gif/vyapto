<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salary_slips', function (Blueprint $table) {
            $decimals = [
                'basic_salary', 'pt_value', 'hra_value', 'special_allow_value', 'stat_bonus_value',
                'perquisite_value', 'exempt_reimburse_value', 'deduction_10_value', 'deduction_16_value',
                'deduction_24_value', 'deduction_via_value', 'net_taxable_income', 'total_tax_payable',
                'total_tax_recovered', 'balance_tax_recoverable',
            ];

            foreach ($decimals as $column) {
                if (! Schema::hasColumn('salary_slips', $column)) {
                    $table->decimal($column, 12, 2)->nullable()->default(0);
                }
            }

            $types = [
                'pt_type', 'hra_type', 'special_allow_type', 'stat_bonus_type', 'perquisite_type',
                'exempt_reimburse_type', 'deduction_10_type', 'deduction_16_type', 'deduction_24_type',
                'deduction_via_type',
            ];

            foreach ($types as $column) {
                if (! Schema::hasColumn('salary_slips', $column)) {
                    $table->string($column, 50)->nullable();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('salary_slips', function (Blueprint $table) {
            $columns = [
                'basic_salary', 'pt_value', 'pt_type', 'hra_value', 'hra_type',
                'special_allow_value', 'special_allow_type', 'stat_bonus_value', 'stat_bonus_type',
                'perquisite_value', 'perquisite_type', 'exempt_reimburse_value', 'exempt_reimburse_type',
                'deduction_10_value', 'deduction_10_type', 'deduction_16_value', 'deduction_16_type',
                'deduction_24_value', 'deduction_24_type', 'deduction_via_value', 'deduction_via_type',
                'net_taxable_income', 'total_tax_payable', 'total_tax_recovered', 'balance_tax_recoverable',
            ];
            $existing = array_filter($columns, fn (string $column) => Schema::hasColumn('salary_slips', $column));
            if ($existing !== []) {
                $table->dropColumn($existing);
            }
        });
    }
};
