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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
	        $table->string('phone', 20)->nullable();
	        $table->string('email')->nullable();
	        $table->text('address')->nullable();
	        $table->string('city', 100)->nullable();
	        $table->string('state', 100)->nullable();
	        $table->decimal('latitude', 10, 8)->nullable();
	        $table->decimal('longitude', 11, 8)->nullable();
	        $table->string('gst_number', 50)->nullable();
	        $table->string('gst_document')->nullable();
	        $table->string('profile_image')->nullable();
	        $table->tinyInteger('status')->default(0); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
