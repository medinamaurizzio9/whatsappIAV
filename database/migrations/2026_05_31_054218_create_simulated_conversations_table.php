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
        Schema::create('simulated_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('initial_menu_option_id')->nullable()->constrained('initial_menu_options')->nullOnDelete();
            $table->string('channel')->default('interno')->index();
            $table->text('client_message');
            $table->text('system_response');
            $table->string('response_type')->index();
            $table->foreignId('derivation_area_id')->nullable()->constrained('derivation_areas')->nullOnDelete();
            $table->timestamp('responded_at')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simulated_conversations');
    }
};
