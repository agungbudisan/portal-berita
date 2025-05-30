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
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content');
            $table->text('image_url')->nullable();
            $table->string('cloudinary_public_id')->nullable();
            $table->string('source')->nullable();
            $table->string('source_url')->nullable();
            $table->enum('status', ['published', 'draft'])->default('published');
            $table->unsignedInteger('views_count')->default(0);
            $table->string('api_id')->nullable();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
