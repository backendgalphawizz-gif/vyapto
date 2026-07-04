<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'otp' => fn () => $table->string('otp', 10)->nullable(),
                'otp_expire_at' => fn () => $table->timestamp('otp_expire_at')->nullable(),
                'fcm_token' => fn () => $table->text('fcm_token')->nullable(),
                'date_of_birth' => fn () => $table->date('date_of_birth')->nullable(),
                'gender' => fn () => $table->string('gender', 20)->nullable(),
                'marital_status' => fn () => $table->string('marital_status', 20)->nullable(),
                'father_name' => fn () => $table->string('father_name')->nullable(),
                'place_of_birth' => fn () => $table->string('place_of_birth')->nullable(),
                'aadhar_card_no' => fn () => $table->string('aadhar_card_no', 12)->nullable()->unique(),
                'aadhar_card_image' => fn () => $table->string('aadhar_card_image')->nullable(),
                'pan_card_no' => fn () => $table->string('pan_card_no', 20)->nullable()->unique(),
                'pan_card_image' => fn () => $table->string('pan_card_image')->nullable(),
                'driving_license_no' => fn () => $table->string('driving_license_no', 50)->nullable(),
                'driving_license_image' => fn () => $table->string('driving_license_image')->nullable(),
                'bank_account_no' => fn () => $table->string('bank_account_no', 16)->nullable()->unique(),
                'ifsc_code' => fn () => $table->string('ifsc_code', 20)->nullable(),
                'bank_name' => fn () => $table->string('bank_name')->nullable(),
                'bank_branch' => fn () => $table->string('bank_branch')->nullable(),
                'bank_proof_image' => fn () => $table->string('bank_proof_image')->nullable(),
                'join_date' => fn () => $table->date('join_date')->nullable(),
            ];

            foreach ($columns as $name => $callback) {
                if (! Schema::hasColumn('users', $name)) {
                    $callback();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'otp', 'otp_expire_at', 'fcm_token', 'date_of_birth', 'gender', 'marital_status',
                'father_name', 'place_of_birth', 'aadhar_card_no', 'aadhar_card_image',
                'pan_card_no', 'pan_card_image', 'driving_license_no', 'driving_license_image',
                'bank_account_no', 'ifsc_code', 'bank_name', 'bank_branch', 'bank_proof_image', 'join_date',
            ];
            $existing = array_filter($columns, fn (string $column) => Schema::hasColumn('users', $column));
            if ($existing !== []) {
                $table->dropColumn($existing);
            }
        });
    }
};
