<?php

namespace App\Contracts;

interface KnowledgeProviderInterface
{
    /**
     * @return array<string, mixed>
     */
    public function search(string $question): array;
}
