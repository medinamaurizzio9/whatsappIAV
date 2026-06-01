<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Services\Commercial\CommercialIntentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadController extends Controller
{
    public function index(): View
    {
        return view('leads.index', [
            'clients' => Client::with(['leadScore', 'leadPipeline.assignedArea'])->latest()->paginate(15),
            'stages' => CommercialIntentService::STAGES,
        ]);
    }

    public function show(Client $client, CommercialIntentService $commercial): View
    {
        return view('leads.show', [
            'client' => $client->load(['leadScore', 'leadEvents.intention', 'leadEvents.derivationArea', 'leadPipeline.assignedArea', 'leadAlerts', 'simulatedConversations.derivationArea']),
            'probabilities' => $commercial->probabilities($client),
            'stages' => CommercialIntentService::STAGES,
        ]);
    }

    public function updateStage(Request $request, Client $client, CommercialIntentService $commercial): RedirectResponse
    {
        $data = $request->validate([
            'stage' => ['required', 'in:'.implode(',', CommercialIntentService::STAGES)],
            'notes' => ['nullable', 'string'],
        ]);

        $commercial->moveStage($client, $data['stage'], $request->user()->id, $data['notes'] ?? null);

        return back()->with('status', 'Etapa actualizada.');
    }
}
