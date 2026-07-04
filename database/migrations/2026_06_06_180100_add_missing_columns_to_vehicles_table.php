<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            if (! Schema::hasColumn('vehicles', 'vehicle_number')) {
                $table->string('vehicle_number', 20)->nullable()->unique()->after('id');
            }
            if (! Schema::hasColumn('vehicles', 'vehicle_type')) {
                $table->string('vehicle_type', 50)->nullable()->after('vehicle_number');
            }
            if (! Schema::hasColumn('vehicles', 'vendor_id')) {
                $table->unsignedBigInteger('vendor_id')->nullable()->after('vehicle_type');
            }
            if (! Schema::hasColumn('vehicles', 'rc_image')) {
                $table->string('rc_image')->nullable()->after('vendor_id');
            }
            if (! Schema::hasColumn('vehicles', 'insurance_image')) {
                $table->text('insurance_image')->nullable()->after('rc_image');
            }
            if (! Schema::hasColumn('vehicles', 'status')) {
                $table->unsignedTinyInteger('status')->default(1)->after('insurance_image');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $columns = ['vehicle_number', 'vehicle_type', 'vendor_id', 'rc_image', 'insurance_image', 'status'];
            $existing = array_filter($columns, fn (string $column) => Schema::hasColumn('vehicles', $column));
            if ($existing !== []) {
                $table->dropColumn($existing);
            }
        });
    }
};
