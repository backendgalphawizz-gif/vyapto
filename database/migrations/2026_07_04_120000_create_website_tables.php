<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_page_sections', function (Blueprint $table) {
            $table->id();
            $table->string('page', 50);
            $table->string('section_key', 100);
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->longText('content')->nullable();
            $table->string('icon', 100)->nullable();
            $table->string('image')->nullable();
            $table->string('link')->nullable();
            $table->json('extra')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->unique(['page', 'section_key']);
        });

        Schema::create('website_services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->string('icon', 100)->nullable();
            $table->string('image')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('website_products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->string('image')->nullable();
            $table->string('link')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('website_career_items', function (Blueprint $table) {
            $table->id();
            $table->string('category', 50);
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->string('image')->nullable();
            $table->string('department')->nullable();
            $table->string('location')->nullable();
            $table->string('link')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('website_blogs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->string('image')->nullable();
            $table->string('author')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('website_contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone', 30)->nullable();
            $table->string('subject')->nullable();
            $table->text('message');
            $table->string('status', 20)->default('new');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_contact_messages');
        Schema::dropIfExists('website_blogs');
        Schema::dropIfExists('website_career_items');
        Schema::dropIfExists('website_products');
        Schema::dropIfExists('website_services');
        Schema::dropIfExists('website_page_sections');
    }
};
