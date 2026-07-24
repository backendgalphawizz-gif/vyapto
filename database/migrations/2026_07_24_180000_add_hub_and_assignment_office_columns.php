<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'hub_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('hub_id')->nullable()->after('office_id');
                if (Schema::hasTable('hubs')) {
                    $table->foreign('hub_id')->references('id')->on('hubs')->nullOnDelete();
                }
            });
        }

        if (Schema::hasTable('assignment_parcel')) {
            Schema::table('assignment_parcel', function (Blueprint $table) {
                if (! Schema::hasColumn('assignment_parcel', 'office_id')) {
                    $table->unsignedBigInteger('office_id')->nullable()->after('hub_id');
                    if (Schema::hasTable('offices')) {
                        $table->foreign('office_id')->references('id')->on('offices')->nullOnDelete();
                    }
                }
            });

            // Allow driver→hub / staff→office assignments (one of the two)
            if (Schema::hasColumn('assignment_parcel', 'hub_id')) {
                try {
                    Schema::table('assignment_parcel', function (Blueprint $table) {
                        $table->unsignedBigInteger('hub_id')->nullable()->change();
                    });
                } catch (\Throwable $e) {
                    // doctrine/dbal may be missing — ignore; nullable insert still works if already nullable
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('assignment_parcel') && Schema::hasColumn('assignment_parcel', 'office_id')) {
            Schema::table('assignment_parcel', function (Blueprint $table) {
                try {
                    $table->dropForeign(['office_id']);
                } catch (\Throwable $e) {
                }
                $table->dropColumn('office_id');
            });
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'hub_id')) {
            Schema::table('users', function (Blueprint $table) {
                try {
                    $table->dropForeign(['hub_id']);
                } catch (\Throwable $e) {
                }
                $table->dropColumn('hub_id');
            });
        }
    }
};
