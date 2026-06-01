<?php

namespace App\Services\AI;

use App\Contracts\AiProviderInterface;
use App\Models\AiProviderSetting;
use Illuminate\Support\Facades\Http;
use Throwable;

abstract class AbstractChatProviderService implements AiProviderInterface
{
    public function __construct(protected AiProviderSetting $setting)
    {
    }

    public function generate(string $question, array $context = []): string
    {
        $response = $this->generateResponse([
            ['role' => 'user', 'content' => $question],
        ], $context);

        return (string) ($response['content'] ?? '');
    }

    public function generateResponse(array $messages, array $options = []): array
    {
        $started = microtime(true);

        try {
            $payload = [
                'model' => $options['model'] ?? $this->setting->model,
                'messages' => $messages,
                'temperature' => (float) ($options['temperature'] ?? $this->setting->temperature),
                'max_tokens' => (int) ($options['max_tokens'] ?? $this->setting->max_tokens),
            ];

            $response = Http::withToken((string) $this->setting->apiKey())
                ->timeout((int) ($options['timeout_seconds'] ?? $this->setting->timeout_seconds))
                ->acceptJson()
                ->post($this->endpoint(), $payload);

            if (! $response->successful()) {
                return $this->failure($response->body(), $started, $response->json() ?? []);
            }

            $raw = $response->json();
            $usage = [
                'input_tokens' => (int) data_get($raw, 'usage.prompt_tokens', 0),
                'output_tokens' => (int) data_get($raw, 'usage.completion_tokens', 0),
                'total_tokens' => (int) data_get($raw, 'usage.total_tokens', 0),
            ];

            return [
                'success' => true,
                'provider' => $this->providerName(),
                'model' => (string) data_get($raw, 'model', $this->setting->model),
                'content' => (string) data_get($raw, 'choices.0.message.content', ''),
                'usage' => $usage,
                'cost_estimated' => $this->estimateCost($usage),
                'response_time_ms' => (int) ((microtime(true) - $started) * 1000),
                'raw' => $raw,
            ];
        } catch (Throwable $exception) {
            report($exception);

            return $this->failure($exception->getMessage(), $started);
        }
    }

    public function classifyIntent(string $message, array $context = []): array
    {
        $messages = [
            ['role' => 'system', 'content' => (string) ($context['prompt'] ?? 'Devuelve JSON valido.')],
            ['role' => 'user', 'content' => $message."\n\nContexto: ".json_encode($context, JSON_UNESCAPED_UNICODE)],
        ];

        return $this->generateResponse($messages, ['temperature' => 0.1]);
    }

    public function estimateCost(array $usage): float
    {
        $input = ((int) ($usage['input_tokens'] ?? 0)) / 1000000;
        $output = ((int) ($usage['output_tokens'] ?? 0)) / 1000000;

        return round(($input * $this->inputTokenPrice()) + ($output * $this->outputTokenPrice()), 6);
    }

    abstract protected function providerName(): string;

    abstract protected function defaultEndpoint(): string;

    protected function inputTokenPrice(): float
    {
        return 0;
    }

    protected function outputTokenPrice(): float
    {
        return 0;
    }

    private function endpoint(): string
    {
        return $this->setting->endpoint ?: $this->defaultEndpoint();
    }

    private function failure(string $message, float $started, array $raw = []): array
    {
        return [
            'success' => false,
            'provider' => $this->providerName(),
            'model' => $this->setting->model,
            'content' => '',
            'usage' => ['input_tokens' => 0, 'output_tokens' => 0, 'total_tokens' => 0],
            'cost_estimated' => 0,
            'response_time_ms' => (int) ((microtime(true) - $started) * 1000),
            'error' => $message,
            'raw' => $raw,
        ];
    }
}
