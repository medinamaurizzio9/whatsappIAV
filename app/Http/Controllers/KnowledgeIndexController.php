<?php

namespace App\Http\Controllers;

use App\Services\RAG\KnowledgeIndexerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class KnowledgeIndexController extends Controller
{
    public function store(Request $request, KnowledgeIndexerService $indexer): RedirectResponse
    {
        $data = $request->validate([
            'scope' => ['required', 'in:all,faqs,documents,products'],
        ]);

        $count = $indexer->reindex($data['scope']);

        return back()->with('status', "Reindexacion completada. Embeddings creados: {$count}.");
    }
}
