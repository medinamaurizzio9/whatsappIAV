<?php

namespace App\Http\Controllers;

use App\Models\AiPromptTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AiPromptTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('ai.prompts.index', ['prompts' => AiPromptTemplate::latest()->paginate(10)]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('ai.prompts.create', ['prompt' => new AiPromptTemplate(['is_active' => true])]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        AiPromptTemplate::create($this->validated($request));

        return redirect()->route('ai-prompts.index')->with('status', 'Prompt creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AiPromptTemplate $aiPrompt): View
    {
        return view('ai.prompts.show', ['prompt' => $aiPrompt]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AiPromptTemplate $aiPrompt): View
    {
        return view('ai.prompts.edit', ['prompt' => $aiPrompt]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AiPromptTemplate $aiPrompt): RedirectResponse
    {
        $aiPrompt->update($this->validated($request));

        return redirect()->route('ai-prompts.index')->with('status', 'Prompt actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AiPromptTemplate $aiPrompt): RedirectResponse
    {
        $aiPrompt->delete();

        return redirect()->route('ai-prompts.index')->with('status', 'Prompt eliminado correctamente.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['respuesta_cliente', 'clasificacion_intencion', 'derivacion', 'seguridad'])],
            'content' => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => false];
    }
}
