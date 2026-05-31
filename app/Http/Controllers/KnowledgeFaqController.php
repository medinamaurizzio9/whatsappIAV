<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeCategory;
use App\Models\KnowledgeFaq;
use App\Models\Intention;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KnowledgeFaqController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('knowledge.faqs.index', [
            'faqs' => KnowledgeFaq::with(['category', 'intentions'])->orderByDesc('priority')->latest()->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('knowledge.faqs.create', [
            'faq' => new KnowledgeFaq(['is_active' => true, 'priority' => 0]),
            'categories' => KnowledgeCategory::where('is_active', true)->orderBy('name')->get(),
            'intentions' => Intention::where('is_active', true)->orderByDesc('priority')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $intentions = $data['intentions'] ?? [];
        unset($data['intentions']);
        $faq = KnowledgeFaq::create($data);
        $faq->intentions()->sync($intentions);

        return redirect()->route('knowledge-faqs.index')->with('status', 'FAQ creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(KnowledgeFaq $knowledgeFaq): View
    {
        return view('knowledge.faqs.show', ['faq' => $knowledgeFaq->load(['category', 'intentions'])]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KnowledgeFaq $knowledgeFaq): View
    {
        return view('knowledge.faqs.edit', [
            'faq' => $knowledgeFaq,
            'categories' => KnowledgeCategory::where('is_active', true)->orderBy('name')->get(),
            'intentions' => Intention::where('is_active', true)->orderByDesc('priority')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KnowledgeFaq $knowledgeFaq): RedirectResponse
    {
        $data = $this->validated($request);
        $intentions = $data['intentions'] ?? [];
        unset($data['intentions']);
        $knowledgeFaq->update($data);
        $knowledgeFaq->intentions()->sync($intentions);

        return redirect()->route('knowledge-faqs.index')->with('status', 'FAQ actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KnowledgeFaq $knowledgeFaq): RedirectResponse
    {
        $knowledgeFaq->delete();

        return redirect()->route('knowledge-faqs.index')->with('status', 'FAQ eliminada correctamente.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'knowledge_category_id' => ['nullable', 'exists:knowledge_categories,id'],
            'question' => ['required', 'string'],
            'answer' => ['required', 'string'],
            'keywords' => ['nullable', 'string'],
            'priority' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'intentions' => ['nullable', 'array'],
            'intentions.*' => ['exists:intentions,id'],
        ]) + ['is_active' => false];
    }
}
