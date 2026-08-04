<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'designation')) {
            Schema::table('users', function (Blueprint $table) {
                $after = Schema::hasColumn('users', 'job_type') ? 'job_type' : 'role_id';
                $table->string('designation', 255)->nullable()->after($after);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'designation')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('designation');
            });
        }
    }
};
