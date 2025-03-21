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
        Schema::create('saved_articles', function (Blueprint $table) {
            $table->id();
            $table->string('article_id')->comment('ID dari News API');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('url');
            $table->string('url_to_image')->nullable();
            $table->string('source_name')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->text('content')->nullable();
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->constrained()->comment('Admin yang menyimpan');
            $table->boolean('is_published')->default(false);
            $table->integer('view_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saved_articles');
    }
};
