<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('api_key');
            $table->string('base_url');
            $table->boolean('is_active')->default(true);
            $table->json('parameters')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_configurations');
    }
};
