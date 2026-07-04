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
        if (! Schema::hasColumn('users', 'job_type')) {
            Schema::table('users', function (Blueprint $table) {
                $after = Schema::hasColumn('users', 'role_id') ? 'role_id' : 'password';
                $table->string('job_type')->nullable()->after($after);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('job_type');
        });
    }
};
