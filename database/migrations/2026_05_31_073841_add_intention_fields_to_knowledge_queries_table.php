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
        Schema::table('knowledge_queries', function (Blueprint $table) {
            $table->foreignId('intention_id')->nullable()->after('user_id')->constrained('intentions')->nullOnDelete();
            $table->decimal('simulated_confidence', 5, 2)->nullable()->after('intention_id');
            $table->string('recommended_action')->nullable()->after('simulated_confidence');
            $table->foreignId('derivation_area_id')->nullable()->after('recommended_action')->constrained('derivation_areas')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('knowledge_queries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('derivation_area_id');
            $table->dropColumn('recommended_action');
            $table->dropColumn('simulated_confidence');
            $table->dropConstrainedForeignId('intention_id');
        });
    }
};
