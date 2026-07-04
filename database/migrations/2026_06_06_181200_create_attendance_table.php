<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('attendance')) {
            return;
        }

        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->date('punch_in_date');
            $table->dateTime('punch_in_time')->nullable();
            $table->dateTime('punch_out_time')->nullable();
            $table->string('punch_in_lat', 50)->nullable();
            $table->string('punch_in_long', 50)->nullable();
            $table->string('punch_in_location')->nullable();
            $table->string('punch_in_exception')->nullable();
            $table->string('punch_in_image')->nullable();
            $table->date('punch_out_date')->nullable();
            $table->string('punch_out_lat', 50)->nullable();
            $table->string('punch_out_long', 50)->nullable();
            $table->string('punch_out_location')->nullable();
            $table->string('punch_out_exception')->nullable();
            $table->string('punch_out_image')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'punch_in_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
