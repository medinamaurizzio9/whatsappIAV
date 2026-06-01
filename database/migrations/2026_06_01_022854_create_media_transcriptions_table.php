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
        Schema::create('media_transcriptions', function (Blueprint $table) {
            $table->id();
            $table->string('source_type')->nullable()->index();
            $table->unsignedBigInteger('source_id')->nullable()->index();
            $table->string('file_path');
            $table->string('media_type')->index();
            $table->longText('transcribed_text')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->string('language', 20)->nullable();
            $table->string('status')->default('pendiente')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_transcriptions');
    }
};
