<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('vehicle_usage')) {
            return;
        }

        Schema::create('vehicle_usage', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_number', 50);
            $table->unsignedBigInteger('user_id');
            $table->string('image')->nullable();
            $table->decimal('kms', 10, 2)->nullable();
            $table->timestamps();

            $table->index(['vehicle_number', 'user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_usage');
    }
};
