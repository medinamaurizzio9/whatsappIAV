<?php

namespace App\Contracts;

interface AiProviderInterface
{
    /**
     * @param array<int, array<string, mixed>> $context
     */
    public function generate(string $question, array $context = []): string;

    /**
     * @param array<int, array{role:string, content:string}> $messages
     * @return array<string, mixed>
     */
    public function generateResponse(array $messages, array $options = []): array;

    /**
     * @return array<string, mixed>
     */
    public function classifyIntent(string $message, array $context = []): array;

    /**
     * @param array<string, int|float> $usage
     */
    public function estimateCost(array $usage): float;
}
