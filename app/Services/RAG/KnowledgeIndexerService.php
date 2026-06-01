<?php

namespace App\Services\RAG;

use App\Models\KnowledgeDocument;
use App\Models\KnowledgeEmbedding;
use App\Models\KnowledgeFaq;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Raffle;
use Illuminate\Support\Collection;

class KnowledgeIndexerService
{
    public function __construct(
        private readonly ChunkingService $chunking,
        private readonly EmbeddingGeneratorService $embeddings,
        private readonly DocumentTextExtractorService $extractor,
    ) {
    }

    public function reindex(string $scope = 'all', ?callable $progress = null): int
    {
        $count = 0;
        $model = (string) config('rag.embedding_model', 'local-hash-v1');

        foreach ($this->sources($scope) as $source) {
            [$type, $id, $content] = $source;
            KnowledgeEmbedding::where('source_type', $type)->where('source_id', $id)->where('embedding_model', $model)->delete();

            foreach ($this->chunking->chunk($content) as $index => $chunk) {
                KnowledgeEmbedding::create([
                    'source_type' => $type,
                    'source_id' => $id,
                    'chunk_index' => $index,
                    'content' => $chunk,
                    'embedding_model' => $model,
                    'embedding_vector' => $this->embeddings->embed($chunk),
                    'tokens' => $this->embeddings->countTokens($chunk),
                ]);
                $count++;
            }

            $progress && $progress($type, $id, $count);
        }

        return $count;
    }

    private function sources(string $scope): Collection
    {
        $items = collect();

        if (in_array($scope, ['all', 'faqs'], true)) {
            KnowledgeFaq::where('is_active', true)->get()->each(fn (KnowledgeFaq $faq) => $items->push(['faq', $faq->id, $faq->question."\n".$faq->answer."\n".$faq->keywords]));
        }

        if (in_array($scope, ['all', 'documents'], true)) {
            KnowledgeDocument::where('is_active', true)->get()->each(fn (KnowledgeDocument $document) => $items->push(['document', $document->id, $document->title."\n".$document->description."\n".$this->extractor->extract($document->file_path, $document->file_type)]));
        }

        if (in_array($scope, ['all', 'products'], true)) {
            Product::where('is_active', true)->get()->each(fn (Product $product) => $items->push(['product', $product->id, $product->name."\n".$product->description."\nPrecio: ".$product->price]));
        }

        if ($scope === 'all') {
            Promotion::where('is_active', true)->get()->each(fn (Promotion $promotion) => $items->push(['promotion', $promotion->id, $promotion->name."\n".$promotion->description]));
            Raffle::where('is_active', true)->get()->each(fn (Raffle $raffle) => $items->push(['raffle', $raffle->id, $raffle->name."\n".$raffle->description."\n".$raffle->prizes."\n".$raffle->rules]));
        }

        return $items;
    }
}
