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
        Schema::create('knowledge_embeddings', function (Blueprint $table) {
            $table->id();
            $table->string('source_type')->index();
            $table->unsignedBigInteger('source_id')->index();
            $table->unsignedInteger('chunk_index')->default(0);
            $table->longText('content');
            $table->string('embedding_model');
            $table->json('embedding_vector');
            $table->unsignedInteger('tokens')->default(0);
            $table->timestamps();
            $table->unique(['source_type', 'source_id', 'chunk_index', 'embedding_model'], 'knowledge_embeddings_source_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_embeddings');
    }
};
