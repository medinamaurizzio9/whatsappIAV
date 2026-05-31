<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KnowledgeCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('knowledge.categories.index', [
            'categories' => KnowledgeCategory::latest()->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('knowledge.categories.create', [
            'category' => new KnowledgeCategory(['is_active' => true]),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        KnowledgeCategory::create($this->validated($request));

        return redirect()->route('knowledge-categories.index')->with('status', 'Categoria creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(KnowledgeCategory $knowledgeCategory): View
    {
        return view('knowledge.categories.show', ['category' => $knowledgeCategory]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KnowledgeCategory $knowledgeCategory): View
    {
        return view('knowledge.categories.edit', ['category' => $knowledgeCategory]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KnowledgeCategory $knowledgeCategory): RedirectResponse
    {
        $knowledgeCategory->update($this->validated($request));

        return redirect()->route('knowledge-categories.index')->with('status', 'Categoria actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KnowledgeCategory $knowledgeCategory): RedirectResponse
    {
        $knowledgeCategory->delete();

        return redirect()->route('knowledge-categories.index')->with('status', 'Categoria eliminada correctamente.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => false];
    }
}
