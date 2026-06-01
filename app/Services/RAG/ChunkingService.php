<?php

namespace App\Services\RAG;

use Illuminate\Support\Str;

class ChunkingService
{
    /**
     * @return array<int, string>
     */
    public function chunk(string $content, ?int $chunkSize = null, ?int $overlap = null): array
    {
        $chunkSize ??= (int) config('rag.chunk_size', 900);
        $overlap ??= (int) config('rag.chunk_overlap', 150);
        $content = trim(preg_replace('/\s+/', ' ', $content) ?? '');

        if ($content === '') {
            return [];
        }

        if (Str::length($content) <= $chunkSize) {
            return [$content];
        }

        $chunks = [];
        $position = 0;
        $step = max(1, $chunkSize - $overlap);

        while ($position < Str::length($content)) {
            $chunks[] = trim(Str::substr($content, $position, $chunkSize));
            $position += $step;
        }

        return array_values(array_filter($chunks));
    }
}
