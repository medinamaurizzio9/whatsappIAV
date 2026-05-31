<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\InitialMenuOption;
use App\Models\SimulatedConversation;
use App\Services\AiRouterService;
use App\Services\KnowledgeBaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InternalChatController extends Controller
{
    public function index(): View
    {
        return view('chat.index', [
            'clients' => Client::orderBy('name')->get(),
            'options' => InitialMenuOption::where('is_active', true)->orderBy('sort_order')->get(),
            'latestConversations' => SimulatedConversation::with(['client', 'derivationArea', 'initialMenuOption'])
                ->latest('responded_at')
                ->limit(10)
                ->get(),
            'result' => session('result'),
        ]);
    }

    public function store(Request $request, AiRouterService $router, KnowledgeBaseService $knowledge): RedirectResponse
    {
        $data = $request->validate([
            'client_id' => ['nullable', 'exists:clients,id'],
            'initial_menu_option_id' => ['required', 'exists:initial_menu_options,id'],
            'client_message' => ['required', 'string'],
        ]);

        $option = InitialMenuOption::with('derivationArea')->findOrFail($data['initial_menu_option_id']);
        $result = $router->route($option, $data['client_message']);
        $knowledgeResult = $knowledge->generarRespuestaLocal($data['client_message']);
        $systemResponse = $knowledgeResult['answer'] ?: $result['message'];
        $responseType = $knowledgeResult['recommended_action'] === 'responder_ia' ? 'ia_simulada' : 'derivacion';
        $derivationAreaId = $knowledgeResult['derivation_area']?->id ?? $result['derivation_area_id'];

        SimulatedConversation::create([
            'client_id' => $data['client_id'] ?? null,
            'initial_menu_option_id' => $option->id,
            'channel' => 'interno',
            'client_message' => $data['client_message'],
            'system_response' => $systemResponse,
            'response_type' => $responseType,
            'derivation_area_id' => $derivationAreaId,
            'responded_at' => now(),
        ]);

        return redirect()->route('chat.index')->with('result', [
            'option' => $option->title,
            'client_message' => $data['client_message'],
            'response_type' => $responseType,
            'message' => $systemResponse,
            'area' => $knowledgeResult['derivation_area']?->name ?? $result['derivation_area']?->name,
            'intention' => $knowledgeResult['intention']?->name,
            'confidence' => $knowledgeResult['confidence'],
            'recommended_action' => $knowledgeResult['recommended_action'],
        ]);
    }
}
