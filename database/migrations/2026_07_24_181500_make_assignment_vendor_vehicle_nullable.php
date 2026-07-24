<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('assignment_parcel')) {
            return;
        }

        // Allow Staff Employee office-only assignments without vendor/vehicle
        try {
            Schema::table('assignment_parcel', function (Blueprint $table) {
                if (Schema::hasColumn('assignment_parcel', 'vendor_id')) {
                    $table->unsignedBigInteger('vendor_id')->nullable()->change();
                }
                if (Schema::hasColumn('assignment_parcel', 'vehicle_id')) {
                    $table->unsignedBigInteger('vehicle_id')->nullable()->change();
                }
            });
        } catch (\Throwable $e) {
            // Fallback without doctrine/dbal
            try {
                DB::statement('ALTER TABLE `assignment_parcel` MODIFY `vendor_id` BIGINT UNSIGNED NULL');
                DB::statement('ALTER TABLE `assignment_parcel` MODIFY `vehicle_id` BIGINT UNSIGNED NULL');
            } catch (\Throwable $e2) {
                // ignore if already nullable
            }
        }
    }

    public function down(): void
    {
        // no-op: keep nullable
    }
};
