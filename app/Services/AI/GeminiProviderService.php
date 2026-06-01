<?php

namespace App\Services\AI;

use App\Contracts\AiProviderInterface;
use App\Models\AiProviderSetting;
use Illuminate\Support\Facades\Http;
use Throwable;

class GeminiProviderService implements AiProviderInterface
{
    public function __construct(private readonly AiProviderSetting $setting)
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
            $apiKey = $this->setting->apiKey();

            if (! $apiKey) {
                return $this->failure('API key de Gemini no configurada.', $started);
            }

            $payload = [
                'contents' => $this->toGeminiContents($messages),
                'generationConfig' => [
                    'temperature' => (float) ($options['temperature'] ?? $this->setting->temperature),
                    'maxOutputTokens' => (int) ($options['max_tokens'] ?? $this->setting->max_tokens),
                ],
            ];

            $response = Http::withHeaders(['x-goog-api-key' => $apiKey])
                ->timeout((int) ($options['timeout_seconds'] ?? $this->setting->timeout_seconds))
                ->acceptJson()
                ->post($this->endpoint(), $payload);

            if (! $response->successful()) {
                return $this->failure($response->json('error.message') ?: $response->body(), $started, $response->json() ?? []);
            }

            $raw = $response->json();
            $usage = [
                'input_tokens' => (int) data_get($raw, 'usageMetadata.promptTokenCount', 0),
                'output_tokens' => (int) data_get($raw, 'usageMetadata.candidatesTokenCount', 0),
                'total_tokens' => (int) data_get($raw, 'usageMetadata.totalTokenCount', 0),
            ];

            return [
                'success' => true,
                'provider' => 'gemini',
                'model' => $this->setting->model,
                'content' => (string) data_get($raw, 'candidates.0.content.parts.0.text', ''),
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
        return $this->generateResponse([
            ['role' => 'user', 'content' => (string) ($context['prompt'] ?? 'Devuelve JSON valido.')."\n\nMensaje: {$message}\n\nContexto: ".json_encode($context, JSON_UNESCAPED_UNICODE)],
        ], ['temperature' => 0.1]);
    }

    public function estimateCost(array $usage): float
    {
        $input = ((int) ($usage['input_tokens'] ?? 0)) / 1000000;
        $output = ((int) ($usage['output_tokens'] ?? 0)) / 1000000;

        return round(($input * 0.30) + ($output * 2.50), 6);
    }

    private function endpoint(): string
    {
        return $this->setting->endpoint ?: "https://generativelanguage.googleapis.com/v1beta/models/{$this->setting->model}:generateContent";
    }

    private function toGeminiContents(array $messages): array
    {
        $text = collect($messages)
            ->map(fn (array $message) => strtoupper((string) ($message['role'] ?? 'user')).": ".(string) ($message['content'] ?? ''))
            ->implode("\n\n");

        return [
            [
                'role' => 'user',
                'parts' => [['text' => $text]],
            ],
        ];
    }

    private function failure(string $message, float $started, array $raw = []): array
    {
        return [
            'success' => false,
            'provider' => 'gemini',
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
