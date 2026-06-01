<?php

namespace App\Services\RAG;

use App\Models\Intention;
use App\Models\KnowledgeEmbedding;
use Illuminate\Support\Collection;

class SemanticSearchService
{
    public function __construct(private readonly EmbeddingGeneratorService $embeddings)
    {
    }

    public function buscarPorSimilitud(string $query, int $limit = null): Collection
    {
        $limit ??= (int) config('rag.semantic_limit', 8);
        $queryVector = $this->embeddings->embed($query);
        $minimum = (float) config('rag.minimum_score', 0.10);

        return KnowledgeEmbedding::query()
            ->get()
            ->map(function (KnowledgeEmbedding $embedding) use ($queryVector) {
                $embedding->score = $this->cosine($queryVector, $embedding->embedding_vector ?? []);
                $embedding->intention_names = $this->intentionsFor($embedding)->pluck('name')->implode(', ');

                return $embedding;
            })
            ->filter(fn (KnowledgeEmbedding $embedding) => $embedding->score >= $minimum)
            ->sortByDesc('score')
            ->take($limit)
            ->values();
    }

    public function buscarDocumentosRelacionados(string $query): Collection
    {
        return $this->buscarPorSimilitud($query)->where('source_type', 'document')->values();
    }

    public function buscarContexto(string $query): array
    {
        return $this->buscarPorSimilitud($query)
            ->map(fn (KnowledgeEmbedding $embedding) => [
                'content' => $embedding->content,
                'score' => round($embedding->score, 4),
                'source_type' => $embedding->source_type,
                'source_id' => $embedding->source_id,
                'chunk_index' => $embedding->chunk_index,
                'intention' => $embedding->intention_names,
            ])
            ->all();
    }

    private function cosine(array $a, array $b): float
    {
        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;
        $length = max(count($a), count($b));

        for ($i = 0; $i < $length; $i++) {
            $av = (float) ($a[$i] ?? 0);
            $bv = (float) ($b[$i] ?? 0);
            $dot += $av * $bv;
            $normA += $av * $av;
            $normB += $bv * $bv;
        }

        return ($normA && $normB) ? $dot / (sqrt($normA) * sqrt($normB)) : 0.0;
    }

    private function intentionsFor(KnowledgeEmbedding $embedding): Collection
    {
        $class = match ($embedding->source_type) {
            'faq' => \App\Models\KnowledgeFaq::class,
            'document' => \App\Models\KnowledgeDocument::class,
            'product' => \App\Models\Product::class,
            'promotion' => \App\Models\Promotion::class,
            'raffle' => \App\Models\Raffle::class,
            default => null,
        };

        if (! $class) {
            return collect();
        }

        $model = $class::with('intentions')->find($embedding->source_id);

        return $model?->intentions ?? collect();
    }
}
