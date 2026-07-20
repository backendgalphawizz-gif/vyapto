<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('website_products', function (Blueprint $table) {
            $table->string('category', 120)->nullable()->after('slug');
            $table->string('subtitle', 180)->nullable()->after('category');
            $table->string('icon', 100)->nullable()->after('content');
            $table->json('features')->nullable()->after('icon');
        });
    }

    public function down(): void
    {
        Schema::table('website_products', function (Blueprint $table) {
            $table->dropColumn(['category', 'subtitle', 'icon', 'features']);
        });
    }
};
