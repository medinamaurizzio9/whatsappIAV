<?php

namespace App\Services\AI;

use App\Models\AiPromptTemplate;
use App\Models\DerivationArea;
use App\Models\Intention;
use App\Services\KnowledgeBaseService;
use Throwable;

class AiIntentClassifierService
{
    public function __construct(
        private readonly AiProviderManager $manager,
        private readonly KnowledgeBaseService $knowledge,
    ) {
    }

    /**
     * @return array{intention:?Intention, confidence:float, recommended_action:string, derivation_area:?DerivationArea, reason:string, ai_response:array<string,mixed>|null}
     */
    public function classify(string $message, string $provider = 'automatico', bool $useAi = true): array
    {
        $fallback = $this->knowledge->detectarIntencionBasica($message) + ['reason' => 'Deteccion local por palabras clave.', 'ai_response' => null];

        if (! $useAi) {
            return $fallback;
        }

        try {
            $intentions = Intention::with('derivationArea')->where('is_active', true)->orderByDesc('priority')->get();
            $prompt = AiPromptTemplate::where('type', 'clasificacion_intencion')->where('is_active', true)->latest()->first()?->content
                ?? 'Clasifica el mensaje. Devuelve JSON valido.';

            $ai = $this->manager->provider($provider);
            $response = $ai->classifyIntent($message, [
                'prompt' => $prompt,
                'intentions' => $intentions->map(fn (Intention $intention) => [
                    'slug' => $intention->slug,
                    'name' => $intention->name,
                    'action' => $intention->default_action,
                    'minimum_confidence' => $intention->minimum_confidence,
                    'derivation_area' => $intention->derivationArea?->name,
                    'keywords' => $intention->keywords,
                ])->values()->all(),
            ]);

            if (! ($response['success'] ?? false)) {
                return $fallback;
            }

            $json = $this->extractJson((string) $response['content']);
            $intention = $intentions->firstWhere('slug', $json['intention_slug'] ?? null) ?: $fallback['intention'];
            $confidence = (float) ($json['confidence'] ?? $fallback['confidence']);
            $area = $intention?->derivationArea ?: $fallback['derivation_area'];
            $action = (string) ($json['action'] ?? $intention?->default_action ?? $fallback['recommended_action']);

            if ($intention && $confidence < (float) $intention->minimum_confidence) {
                $action = Intention::ACTION_DERIVE;
                $area = $area ?: DerivationArea::where('name', 'Ventas')->first();
            }

            [$action, $area] = $this->enforceRules($intention, $action, $area);

            return [
                'intention' => $intention,
                'confidence' => round($confidence, 2),
                'recommended_action' => $action,
                'derivation_area' => $area,
                'reason' => (string) ($json['reason'] ?? 'Clasificacion IA.'),
                'ai_response' => $response,
            ];
        } catch (Throwable $exception) {
            report($exception);

            return $fallback;
        }
    }

    private function extractJson(string $content): array
    {
        if (preg_match('/\{.*\}/s', $content, $matches)) {
            return json_decode($matches[0], true) ?: [];
        }

        return json_decode($content, true) ?: [];
    }

    private function enforceRules(?Intention $intention, string $action, ?DerivationArea $area): array
    {
        if (! $intention) {
            return [$action, $area];
        }

        if ($intention->slug === 'inversion') {
            return [Intention::ACTION_DERIVE, DerivationArea::where('name', 'Gerencia Comercial')->first()];
        }

        if (in_array($intention->slug, ['reclamo', 'garantia'], true)) {
            return [Intention::ACTION_DERIVE, DerivationArea::where('name', 'Soporte')->first()];
        }

        return [$action, $area];
    }
}
