<?php

namespace App\Contracts;

interface AiProviderInterface
{
    /**
     * @param array<int, array<string, mixed>> $context
     */
    public function generate(string $question, array $context = []): string;
}
