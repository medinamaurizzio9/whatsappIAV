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
        Schema::create('ai_provider_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('provider')->index();
            $table->string('model');
            $table->text('api_key_encrypted')->nullable();
            $table->string('api_key_last_four', 4)->nullable();
            $table->string('endpoint')->nullable();
            $table->decimal('temperature', 3, 2)->default(0.30);
            $table->unsignedInteger('max_tokens')->default(800);
            $table->unsignedInteger('timeout_seconds')->default(30);
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_default')->default(false)->index();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_provider_settings');
    }
};
