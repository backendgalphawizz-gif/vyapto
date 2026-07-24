<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('assignment_parcel')) {
            return;
        }

        Schema::table('assignment_parcel', function (Blueprint $table) {
            if (! Schema::hasColumn('assignment_parcel', 'from_date')) {
                $table->date('from_date')->nullable()->after('assignment_date');
            }
            if (! Schema::hasColumn('assignment_parcel', 'to_date')) {
                $table->date('to_date')->nullable()->after('from_date');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('assignment_parcel')) {
            return;
        }

        Schema::table('assignment_parcel', function (Blueprint $table) {
            if (Schema::hasColumn('assignment_parcel', 'to_date')) {
                $table->dropColumn('to_date');
            }
            if (Schema::hasColumn('assignment_parcel', 'from_date')) {
                $table->dropColumn('from_date');
            }
        });
    }
};
