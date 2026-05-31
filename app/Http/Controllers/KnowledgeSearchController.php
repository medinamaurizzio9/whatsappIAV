<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeQuery;
use App\Models\DerivationArea;
use App\Models\Intention;
use App\Services\KnowledgeBaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KnowledgeSearchController extends Controller
{
    public function index(): View
    {
        return view('knowledge.search.index', [
            'result' => session('result'),
            'audits' => KnowledgeQuery::with(['user', 'intention', 'derivationArea'])
                ->when(request('intention_id'), fn ($query, $value) => $query->where('intention_id', $value))
                ->when(request('recommended_action'), fn ($query, $value) => $query->where('recommended_action', $value))
                ->when(request('derivation_area_id'), fn ($query, $value) => $query->where('derivation_area_id', $value))
                ->when(request('from'), fn ($query, $value) => $query->whereDate('queried_at', '>=', $value))
                ->when(request('to'), fn ($query, $value) => $query->whereDate('queried_at', '<=', $value))
                ->latest('queried_at')
                ->paginate(10)
                ->withQueryString(),
            'intentions' => Intention::where('is_active', true)->orderBy('name')->get(),
            'areas' => DerivationArea::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function search(Request $request, KnowledgeBaseService $knowledge): RedirectResponse
    {
        $data = $request->validate([
            'question' => ['required', 'string', 'max:1000'],
        ]);

        $result = $knowledge->generarRespuestaLocal($data['question']);

        KnowledgeQuery::create([
            'user_id' => $request->user()->id,
            'intention_id' => $result['intention']?->id,
            'simulated_confidence' => $result['confidence'],
            'recommended_action' => $result['recommended_action'],
            'derivation_area_id' => $result['derivation_area']?->id,
            'question' => $data['question'],
            'generated_answer' => $result['answer'],
            'sources' => $result['sources'],
            'queried_at' => now(),
        ]);

        return redirect()->route('knowledge-search.index')->with('result', [
            'question' => $data['question'],
            'answer' => $result['answer'],
            'intention' => $result['intention']?->name,
            'confidence' => $result['confidence'],
            'recommended_action' => $result['recommended_action'],
            'derivation_area' => $result['derivation_area']?->name,
            'sources' => $result['sources'],
            'faqs' => $result['faqs']->map(fn ($faq) => ['id' => $faq->id, 'question' => $faq->question])->all(),
            'documents' => $result['documents']->map(fn ($doc) => ['id' => $doc->id, 'title' => $doc->title, 'file' => $doc->original_filename])->all(),
        ]);
    }
}
