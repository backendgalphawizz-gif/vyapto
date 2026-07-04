<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('parcel_detail')) {
            return;
        }

        Schema::create('parcel_detail', function (Blueprint $table) {
            $table->id();
            $table->string('parcel_id', 100)->nullable();
            $table->unsignedBigInteger('assignment_parcel_id');
            $table->unsignedBigInteger('user_id');
            $table->string('status', 32)->default('assigned');
            $table->timestamps();

            $table->index(['assignment_parcel_id', 'parcel_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parcel_detail');
    }
};
