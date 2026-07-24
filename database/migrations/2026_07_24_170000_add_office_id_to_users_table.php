<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users') || Schema::hasColumn('users', 'office_id')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('office_id')->nullable()->after('department_id');

            if (Schema::hasTable('offices')) {
                $table->foreign('office_id')->references('id')->on('offices')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasColumn('users', 'office_id')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            try {
                $table->dropForeign(['office_id']);
            } catch (\Throwable $e) {
                // ignore if FK was never created
            }
            $table->dropColumn('office_id');
        });
    }
};
