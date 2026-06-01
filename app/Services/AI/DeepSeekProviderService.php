<?php

namespace App\Services\AI;

class DeepSeekProviderService extends AbstractChatProviderService
{
    protected function providerName(): string
    {
        return 'deepseek';
    }

    protected function defaultEndpoint(): string
    {
        return 'https://api.deepseek.com/chat/completions';
    }

    protected function inputTokenPrice(): float
    {
        return 0.28;
    }

    protected function outputTokenPrice(): float
    {
        return 0.42;
    }
}
