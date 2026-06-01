<?php

namespace App\Services\RAG;

use Illuminate\Support\Str;

class EmbeddingGeneratorService
{
    /**
     * Local deterministic embedding. Replace with provider embeddings later.
     *
     * @return array<int, float>
     */
    public function embed(string $content): array
    {
        $dimensions = (int) config('rag.embedding_dimensions', 128);
        $vector = array_fill(0, $dimensions, 0.0);
        $tokens = $this->tokens($content);

        foreach ($tokens as $token) {
            $index = abs(crc32($token)) % $dimensions;
            $vector[$index] += 1.0;
        }

        $norm = sqrt(array_sum(array_map(fn (float $value) => $value * $value, $vector))) ?: 1.0;

        return array_map(fn (float $value) => round($value / $norm, 6), $vector);
    }

    public function countTokens(string $content): int
    {
        return count($this->tokens($content));
    }

    /**
     * @return array<int, string>
     */
    private function tokens(string $content): array
    {
        return collect(preg_split('/\s+/', Str::lower(Str::ascii($content))) ?: [])
            ->map(fn (string $token) => trim($token, " \t\n\r\0\x0B.,;:!?()[]{}\"'"))
            ->filter(fn (string $token) => strlen($token) >= 2)
            ->values()
            ->all();
    }
}
