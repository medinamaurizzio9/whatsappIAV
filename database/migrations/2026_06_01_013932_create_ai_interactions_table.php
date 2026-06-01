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
        Schema::create('ai_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('provider')->nullable()->index();
            $table->string('model')->nullable();
            $table->longText('question');
            $table->longText('response')->nullable();
            $table->foreignId('intention_id')->nullable()->constrained('intentions')->nullOnDelete();
            $table->decimal('confidence', 5, 2)->nullable();
            $table->string('action')->nullable()->index();
            $table->foreignId('derivation_area_id')->nullable()->constrained('derivation_areas')->nullOnDelete();
            $table->json('sources_json')->nullable();
            $table->unsignedInteger('input_tokens')->default(0);
            $table->unsignedInteger('output_tokens')->default(0);
            $table->unsignedInteger('total_tokens')->default(0);
            $table->decimal('cost_estimated', 12, 6)->default(0);
            $table->unsignedInteger('response_time_ms')->default(0);
            $table->boolean('success')->default(false)->index();
            $table->text('error_message')->nullable();
            $table->json('raw_response_json')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_interactions');
    }
};
