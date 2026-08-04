<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'location_from_date')) {
                $table->date('location_from_date')->nullable()->after('office_id');
            }
            if (! Schema::hasColumn('users', 'location_to_date')) {
                $table->date('location_to_date')->nullable()->after('location_from_date');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'location_to_date')) {
                $table->dropColumn('location_to_date');
            }
            if (Schema::hasColumn('users', 'location_from_date')) {
                $table->dropColumn('location_from_date');
            }
        });
    }
};
