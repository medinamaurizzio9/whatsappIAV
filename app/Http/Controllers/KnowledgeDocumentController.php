<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeCategory;
use App\Models\KnowledgeDocument;
use App\Models\Intention;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class KnowledgeDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('knowledge.documents.index', [
            'documents' => KnowledgeDocument::with(['category', 'intentions'])->latest('uploaded_at')->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('knowledge.documents.create', [
            'document' => new KnowledgeDocument(['is_active' => true]),
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
        $file = $request->file('file');
        $data['file_path'] = $file->store('knowledge/documents', 'public');
        $data['original_filename'] = $file->getClientOriginalName();
        $data['file_type'] = strtolower($file->getClientOriginalExtension());
        $data['file_size'] = $file->getSize();
        $data['uploaded_at'] = now();
        unset($data['file']);

        $intentions = $data['intentions'] ?? [];
        unset($data['file'], $data['intentions']);
        $document = KnowledgeDocument::create($data);
        $document->intentions()->sync($intentions);
        return redirect()->route('knowledge-documents.index')->with('status', 'Documento cargado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(KnowledgeDocument $knowledgeDocument): View
    {
        return view('knowledge.documents.show', ['document' => $knowledgeDocument->load(['category', 'intentions'])]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KnowledgeDocument $knowledgeDocument): View
    {
        return view('knowledge.documents.edit', [
            'document' => $knowledgeDocument,
            'categories' => KnowledgeCategory::where('is_active', true)->orderBy('name')->get(),
            'intentions' => Intention::where('is_active', true)->orderByDesc('priority')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KnowledgeDocument $knowledgeDocument): RedirectResponse
    {
        $data = $this->validated($request, false);

        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($knowledgeDocument->file_path);
            $file = $request->file('file');
            $data['file_path'] = $file->store('knowledge/documents', 'public');
            $data['original_filename'] = $file->getClientOriginalName();
            $data['file_type'] = strtolower($file->getClientOriginalExtension());
            $data['file_size'] = $file->getSize();
            $data['uploaded_at'] = now();
        }

        $intentions = $data['intentions'] ?? [];
        unset($data['file'], $data['intentions']);
        $knowledgeDocument->update($data);
        $knowledgeDocument->intentions()->sync($intentions);

        return redirect()->route('knowledge-documents.index')->with('status', 'Documento actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KnowledgeDocument $knowledgeDocument): RedirectResponse
    {
        $knowledgeDocument->delete();

        return redirect()->route('knowledge-documents.index')->with('status', 'Documento eliminado correctamente.');
    }

    private function validated(Request $request, bool $fileRequired = true): array
    {
        return $request->validate([
            'knowledge_category_id' => ['required', 'exists:knowledge_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'file' => [$fileRequired ? 'required' : 'nullable', 'file', 'mimes:pdf,docx,txt,png,jpg,jpeg', 'max:10240'],
            'is_active' => ['nullable', 'boolean'],
            'intentions' => ['nullable', 'array'],
            'intentions.*' => ['exists:intentions,id'],
        ]) + ['is_active' => false];
    }
}
