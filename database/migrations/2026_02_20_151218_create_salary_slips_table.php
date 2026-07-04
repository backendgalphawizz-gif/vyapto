<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('salary_slips', function (Blueprint $table) {
            $table->id('slip_id');

            $table->unsignedBigInteger('employee_id')
                  ->comment('Reference to employee (no FK constraint)');

            $table->unsignedTinyInteger('month')
                  ->comment('1 to 12');

            $table->unsignedSmallInteger('year')
                  ->comment('e.g. 2025');

            $table->string('file_path')
                  ->comment('Stored PDF path');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_slips');
    }
};
