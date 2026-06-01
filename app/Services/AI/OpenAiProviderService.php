<?php

namespace App\Services\AI;

class OpenAiProviderService extends AbstractChatProviderService
{
    protected function providerName(): string
    {
        return 'openai';
    }

    protected function defaultEndpoint(): string
    {
        return 'https://api.openai.com/v1/chat/completions';
    }

    protected function inputTokenPrice(): float
    {
        return 1.25;
    }

    protected function outputTokenPrice(): float
    {
        return 10.00;
    }
}
