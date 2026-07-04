<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            if (! Schema::hasColumn('vendors', 'business_name')) {
                $table->string('business_name')->nullable()->after('name');
            }
            if (! Schema::hasColumn('vendors', 'business_mobile')) {
                $table->string('business_mobile', 20)->nullable()->after('phone');
            }
            if (! Schema::hasColumn('vendors', 'pan_number')) {
                $table->string('pan_number', 20)->nullable();
            }
            if (! Schema::hasColumn('vendors', 'aadhar_number')) {
                $table->string('aadhar_number', 12)->nullable();
            }
            if (! Schema::hasColumn('vendors', 'buisness_pan')) {
                $table->string('buisness_pan', 20)->nullable();
            }
            if (! Schema::hasColumn('vendors', 'business_pan')) {
                $table->string('business_pan', 20)->nullable();
            }
            if (! Schema::hasColumn('vendors', 'cancelled_cheque_details')) {
                $table->text('cancelled_cheque_details')->nullable();
            }
            if (! Schema::hasColumn('vendors', 'cancelled_cheque_image')) {
                $table->string('cancelled_cheque_image')->nullable();
            }
            if (! Schema::hasColumn('vendors', 'bank_account_number')) {
                $table->string('bank_account_number', 20)->nullable();
            }
            if (! Schema::hasColumn('vendors', 'bank_ifsc_code')) {
                $table->string('bank_ifsc_code', 11)->nullable();
            }
            if (! Schema::hasColumn('vendors', 'bank_account_image')) {
                $table->string('bank_account_image')->nullable();
            }
            if (! Schema::hasColumn('vendors', 'bank_statement_image')) {
                $table->string('bank_statement_image')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $columns = [
                'business_name', 'business_mobile', 'pan_number', 'aadhar_number',
                'buisness_pan', 'business_pan', 'cancelled_cheque_details',
                'cancelled_cheque_image', 'bank_account_number', 'bank_ifsc_code',
                'bank_account_image', 'bank_statement_image',
            ];
            $existing = array_filter($columns, fn (string $column) => Schema::hasColumn('vendors', $column));
            if ($existing !== []) {
                $table->dropColumn($existing);
            }
        });
    }
};
