<?php

namespace App\Http\Controllers;

use App\Models\AiInteraction;
use App\Models\AiProviderSetting;
use App\Models\Client;
use App\Services\AI\AiKnowledgeResponderService;
use App\Services\Commercial\CommercialIntentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AiSandboxController extends Controller
{
    public function index(): View
    {
        return view('ai.sandbox.index', [
            'providers' => AiProviderSetting::where('is_active', true)->orderBy('provider')->get(),
            'clients' => Client::orderBy('name')->get(),
            'result' => session('result'),
            'comparison' => session('comparison'),
        ]);
    }

    public function run(Request $request, AiKnowledgeResponderService $responder): RedirectResponse
    {
        $data = $request->validate([
            'question' => ['required', 'string', 'max:2000'],
            'provider' => ['required', 'in:automatico,openai,deepseek,gemini'],
            'use_knowledge' => ['nullable', 'boolean'],
            'use_intent' => ['nullable', 'boolean'],
            'compare' => ['nullable', 'boolean'],
            'client_id' => ['nullable', 'exists:clients,id'],
        ]);

        if ($request->boolean('compare')) {
            $comparison = [];
            foreach (['openai', 'deepseek', 'gemini'] as $provider) {
                $comparison[$provider] = $this->respondAndStore($request, $responder, $data['question'], $provider, $data);
            }

            return redirect()->route('ai-sandbox.index')->with('comparison', $comparison);
        }

        $result = $this->respondAndStore($request, $responder, $data['question'], $data['provider'], $data);

        return redirect()->route('ai-sandbox.index')->with('result', $result);
    }

    private function respondAndStore(Request $request, AiKnowledgeResponderService $responder, string $question, string $provider, array $data): array
    {
        $result = $responder->respond($question, [
            'provider' => $provider,
            'use_knowledge' => (bool) ($data['use_knowledge'] ?? false),
            'use_intent' => (bool) ($data['use_intent'] ?? false),
        ]);

        AiInteraction::create([
            'user_id' => $request->user()->id,
            'provider' => $result['provider'] ?? $provider,
            'model' => $result['model'] ?? null,
            'question' => $question,
            'response' => $result['content'] ?? null,
            'intention_id' => $result['intention']?->id,
            'confidence' => $result['confidence'] ?? null,
            'action' => $result['recommended_action'] ?? null,
            'derivation_area_id' => $result['derivation_area']?->id,
            'sources_json' => $result['sources'] ?? [],
            'input_tokens' => (int) data_get($result, 'usage.input_tokens', 0),
            'output_tokens' => (int) data_get($result, 'usage.output_tokens', 0),
            'total_tokens' => (int) data_get($result, 'usage.total_tokens', 0),
            'cost_estimated' => (float) ($result['cost_estimated'] ?? 0),
            'response_time_ms' => (int) ($result['response_time_ms'] ?? 0),
            'success' => (bool) ($result['success'] ?? false),
            'error_message' => $result['error'] ?? null,
            'raw_response_json' => $result['raw'] ?? [],
        ]);

        if (! empty($data['client_id'])) {
            app(CommercialIntentService::class)->registerEvent(Client::findOrFail($data['client_id']), $question, $result['intention'] ?? null);
        }

        return $result;
    }
}
