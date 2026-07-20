<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('website_services', function (Blueprint $table) {
            $table->string('category', 120)->nullable()->after('slug');
            $table->string('subtitle', 180)->nullable()->after('category');
            $table->json('features')->nullable()->after('content');
        });
    }

    public function down(): void
    {
        Schema::table('website_services', function (Blueprint $table) {
            $table->dropColumn(['category', 'subtitle', 'features']);
        });
    }
};
