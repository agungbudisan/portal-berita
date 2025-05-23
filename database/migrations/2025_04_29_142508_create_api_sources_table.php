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
        Schema::create('api_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->string('api_key')->nullable();
            $table->json('params')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamp('last_sync')->nullable();
            $table->integer('news_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_sources');
    }
};
