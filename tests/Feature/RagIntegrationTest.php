<?php

namespace Tests\Feature;

use App\Models\KnowledgeCategory;
use App\Models\KnowledgeDocument;
use App\Models\KnowledgeEmbedding;
use App\Models\KnowledgeFaq;
use App\Models\KnowledgeFeedback;
use App\Services\RAG\ChunkingService;
use App\Services\RAG\DocumentTextExtractorService;
use App\Services\RAG\EmbeddingGeneratorService;
use App\Services\RAG\KnowledgeIndexerService;
use App\Services\RAG\SemanticSearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RagIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_chunking_creates_overlapped_chunks(): void
    {
        $chunks = app(ChunkingService::class)->chunk(str_repeat('oro ', 100), 80, 20);

        $this->assertGreaterThan(1, count($chunks));
    }

    public function test_embeddings_are_deterministic(): void
    {
        $service = app(EmbeddingGeneratorService::class);

        $this->assertSame($service->embed('anillo oro'), $service->embed('anillo oro'));
    }

    public function test_reindex_and_semantic_search(): void
    {
        $this->seed();
        KnowledgeFaq::create(['question' => 'Precio del anillo', 'answer' => 'El anillo demo cuesta 1500.', 'is_active' => true]);

        app(KnowledgeIndexerService::class)->reindex('faqs');
        $results = app(SemanticSearchService::class)->buscarPorSimilitud('precio anillo');

        $this->assertGreaterThan(0, KnowledgeEmbedding::count());
        $this->assertNotEmpty($results);
    }

    public function test_feedback_can_be_saved(): void
    {
        KnowledgeFeedback::create(['question' => 'Pregunta', 'generated_answer' => 'Respuesta', 'rating' => 5]);

        $this->assertDatabaseHas('knowledge_feedback', ['rating' => 5]);
    }

    public function test_ocr_placeholder_for_images(): void
    {
        Storage::fake('public');
        Storage::disk('public')->putFileAs('knowledge/documents', UploadedFile::fake()->image('joya.jpg'), 'joya.jpg');

        $text = app(DocumentTextExtractorService::class)->extract('knowledge/documents/joya.jpg', 'jpg');

        $this->assertStringContainsString('OCR pendiente', $text);
    }

    public function test_documents_can_be_indexed(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('knowledge/documents/test.txt', 'catalogo de joyas de oro');
        $category = KnowledgeCategory::create(['name' => 'Docs', 'is_active' => true]);
        KnowledgeDocument::create([
            'knowledge_category_id' => $category->id,
            'title' => 'Catalogo',
            'file_path' => 'knowledge/documents/test.txt',
            'original_filename' => 'test.txt',
            'file_type' => 'txt',
            'is_active' => true,
            'uploaded_at' => now(),
        ]);

        $count = app(KnowledgeIndexerService::class)->reindex('documents');

        $this->assertGreaterThan(0, $count);
    }
}
