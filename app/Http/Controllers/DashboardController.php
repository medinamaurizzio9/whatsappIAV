<?php

namespace App\Http\Controllers;

use App\Models\Client;
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
            'topIntention' => Intention::query()
                ->select('intentions.*', DB::raw('count(knowledge_queries.id) as queries_count'))
                ->leftJoin('knowledge_queries', 'knowledge_queries.intention_id', '=', 'intentions.id')
                ->groupBy('intentions.id')
                ->orderByDesc('queries_count')
                ->first(),
            'queriesByIntention' => KnowledgeQuery::with('intention')
                ->select('intention_id', DB::raw('count(*) as total'))
                ->whereNotNull('intention_id')
                ->groupBy('intention_id')
                ->orderByDesc('total')
                ->limit(5)
                ->get(),
            'derivedKnowledgeQueries' => KnowledgeQuery::whereIn('recommended_action', ['derivar', 'responder_y_derivar'])->count(),
            'aiKnowledgeQueries' => KnowledgeQuery::where('recommended_action', 'responder_ia')->count(),
            'latestConversations' => SimulatedConversation::with(['client', 'derivationArea', 'initialMenuOption'])
                ->latest('responded_at')
                ->limit(8)
                ->get(),
        ]);
    }
}
