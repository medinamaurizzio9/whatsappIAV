<?php

namespace App\Services\AI;

use App\Models\AiPromptTemplate;
use App\Models\DerivationArea;
use App\Models\Intention;
use App\Services\KnowledgeBaseService;
use Throwable;

class AiKnowledgeResponderService
{
    public function __construct(
        private readonly AiProviderManager $manager,
        private readonly AiIntentClassifierService $classifier,
        private readonly KnowledgeBaseService $knowledge,
    ) {
    }

    /**
     * @return array<string,mixed>
     */
    public function respond(string $question, array $options = []): array
    {
        $started = microtime(true);
        $provider = (string) ($options['provider'] ?? 'automatico');
        $useKnowledge = (bool) ($options['use_knowledge'] ?? true);
        $useIntent = (bool) ($options['use_intent'] ?? true);

        $classification = $this->classifier->classify($question, $provider, $useIntent);
        $knowledge = $useKnowledge ? $this->knowledge->generarRespuestaLocal($question) : [
            'sources' => [],
            'faqs' => collect(),
            'products' => collect(),
            'documents' => collect(),
            'promotions' => collect(),
            'raffles' => collect(),
        ];

        $intention = $classification['intention'];
        $action = $classification['recommended_action'];
        $area = $classification['derivation_area'];

        if ($intention && $this->mustDerive($intention)) {
            return $this->decisionOnly($question, $classification, $knowledge, (int) ((microtime(true) - $started) * 1000));
        }

        if (empty($knowledge['sources']) && $useKnowledge) {
            $classification['recommended_action'] = Intention::ACTION_DERIVE;
            $classification['derivation_area'] = $area ?: DerivationArea::where('name', 'Ventas')->first();

            return $this->decisionOnly($question, $classification, $knowledge, (int) ((microtime(true) - $started) * 1000), 'No encontre fuentes suficientes en la base de conocimiento. Derivo la consulta al area correspondiente.');
        }

        try {
            $prompt = AiPromptTemplate::where('type', 'respuesta_cliente')->where('is_active', true)->latest()->first()?->content
                ?? 'Responde usando solo el contexto proporcionado.';

            $messages = [
                ['role' => 'system', 'content' => $prompt],
                ['role' => 'user', 'content' => $this->buildUserPrompt($question, $classification, $knowledge)],
            ];

            $ai = $this->manager->provider($provider);
            $response = $ai->generateResponse($messages);

            if (! ($response['success'] ?? false)) {
                return $this->withAiResponse($question, $classification, $knowledge, $response, (int) ((microtime(true) - $started) * 1000), false);
            }

            return $this->withAiResponse($question, $classification, $knowledge, $response, (int) ((microtime(true) - $started) * 1000), true);
        } catch (Throwable $exception) {
            report($exception);

            return $this->withAiResponse($question, $classification, $knowledge, [
                'success' => false,
                'provider' => $provider,
                'model' => null,
                'content' => '',
                'usage' => ['input_tokens' => 0, 'output_tokens' => 0, 'total_tokens' => 0],
                'cost_estimated' => 0,
                'error' => $exception->getMessage(),
                'raw' => [],
            ], (int) ((microtime(true) - $started) * 1000), false);
        }
    }

    private function mustDerive(Intention $intention): bool
    {
        return in_array($intention->slug, ['inversion', 'reclamo', 'garantia'], true);
    }

    private function buildUserPrompt(string $question, array $classification, array $knowledge): string
    {
        return "Pregunta del cliente: {$question}\n\n".
            'Intencion: '.($classification['intention']?->name ?? 'Sin clasificar')."\n".
            'Accion recomendada: '.$classification['recommended_action']."\n".
            'Area: '.($classification['derivation_area']?->name ?? '-')."\n\n".
            "Fuentes disponibles:\n".json_encode($knowledge['sources'], JSON_UNESCAPED_UNICODE)."\n\n".
            "Contexto local:\n".$knowledge['answer'];
    }

    private function decisionOnly(string $question, array $classification, array $knowledge, int $elapsed, ?string $message = null): array
    {
        $intention = $classification['intention'];
        $area = $classification['derivation_area'];
        $content = $message ?: match ($intention?->slug) {
            'inversion' => 'Tu consulta de inversion sera derivada a Gerencia Comercial para atencion especializada.',
            'reclamo', 'garantia' => 'Tu caso sera derivado a Soporte para revision y seguimiento.',
            default => 'Tu consulta sera derivada al area correspondiente.',
        };

        return [
            'success' => true,
            'provider' => 'local_rules',
            'model' => 'rules',
            'content' => $content,
            'usage' => ['input_tokens' => 0, 'output_tokens' => 0, 'total_tokens' => 0],
            'cost_estimated' => 0,
            'response_time_ms' => $elapsed,
            'intention' => $intention,
            'confidence' => $classification['confidence'],
            'recommended_action' => $classification['recommended_action'],
            'derivation_area' => $area,
            'sources' => $knowledge['sources'],
            'raw' => [],
        ];
    }

    private function withAiResponse(string $question, array $classification, array $knowledge, array $response, int $elapsed, bool $success): array
    {
        return $response + [
            'response_time_ms' => $elapsed,
            'intention' => $classification['intention'],
            'confidence' => $classification['confidence'],
            'recommended_action' => $classification['recommended_action'],
            'derivation_area' => $classification['derivation_area'],
            'sources' => $knowledge['sources'],
            'success' => $success,
        ];
    }
}
