<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\AiInteraction;
use App\Models\KnowledgeDocument;
use App\Models\KnowledgeFaq;
use App\Models\KnowledgeQuery;
use App\Models\Product;
use App\Models\Intention;
use App\Models\SimulatedConversation;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard.index', [
            'totalConversations' => SimulatedConversation::count(),
            'totalClients' => Client::count(),
            'totalDerivations' => SimulatedConversation::where('response_type', 'derivacion')->count(),
            'totalAiTests' => SimulatedConversation::where('response_type', 'ia_simulada')->count(),
            'totalKnowledgeDocuments' => KnowledgeDocument::count(),
            'totalActiveFaqs' => KnowledgeFaq::where('is_active', true)->count(),
            'totalActiveProducts' => Product::where('is_active', true)->count(),
            'totalKnowledgeQueries' => KnowledgeQuery::count(),
            'topIntention' => $this->topIntention(),
            'queriesByIntention' => KnowledgeQuery::with('intention')
                ->select('intention_id', DB::raw('count(*) as total'))
                ->whereNotNull('intention_id')
                ->groupBy('intention_id')
                ->orderByDesc('total')
                ->limit(5)
                ->get(),
            'derivedKnowledgeQueries' => KnowledgeQuery::whereIn('recommended_action', ['derivar', 'responder_y_derivar'])->count(),
            'aiKnowledgeQueries' => KnowledgeQuery::where('recommended_action', 'responder_ia')->count(),
            'totalAiInteractions' => AiInteraction::count(),
            'totalAiCost' => AiInteraction::sum('cost_estimated'),
            'topAiProvider' => AiInteraction::select('provider', DB::raw('count(*) as total'))->whereNotNull('provider')->groupBy('provider')->orderByDesc('total')->first(),
            'aiDerivations' => AiInteraction::whereIn('action', ['derivar', 'responder_y_derivar'])->count(),
            'aiErrors' => AiInteraction::where('success', false)->count(),
            'latestConversations' => SimulatedConversation::with(['client', 'derivationArea', 'initialMenuOption'])
                ->latest('responded_at')
                ->limit(8)
                ->get(),
        ]);
    }

    private function topIntention(): ?Intention
    {
        $row = KnowledgeQuery::select('intention_id', DB::raw('count(*) as queries_count'))
            ->whereNotNull('intention_id')
            ->groupBy('intention_id')
            ->orderByDesc('queries_count')
            ->first();

        if (! $row) {
            return null;
        }

        $intention = Intention::find($row->intention_id);
        $intention?->setAttribute('queries_count', $row->queries_count);

        return $intention;
    }
}
