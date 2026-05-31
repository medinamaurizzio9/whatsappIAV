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
        Schema::dropIfExists('intention_raffle');
        Schema::dropIfExists('intention_promotion');
        Schema::dropIfExists('intention_product');
        Schema::dropIfExists('intention_knowledge_document');
        Schema::dropIfExists('intention_knowledge_faq');

        foreach ([
            'intention_knowledge_faq' => ['knowledge_faq_id', 'knowledge_faqs'],
            'intention_knowledge_document' => ['knowledge_document_id', 'knowledge_documents'],
            'intention_product' => ['product_id', 'products'],
            'intention_promotion' => ['promotion_id', 'promotions'],
            'intention_raffle' => ['raffle_id', 'raffles'],
        ] as $tableName => [$foreignKey, $relatedTable]) {
            Schema::create($tableName, function (Blueprint $table) use ($foreignKey, $relatedTable, $tableName) {
                $table->id();
                $table->foreignId('intention_id')->constrained('intentions')->cascadeOnDelete();
                $table->foreignId($foreignKey)->constrained($relatedTable)->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['intention_id', $foreignKey], $tableName.'_uniq');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intention_raffle');
        Schema::dropIfExists('intention_promotion');
        Schema::dropIfExists('intention_product');
        Schema::dropIfExists('intention_knowledge_document');
        Schema::dropIfExists('intention_knowledge_faq');
    }
};
