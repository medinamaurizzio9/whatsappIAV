<?php

namespace App\Http\Controllers;

use App\Models\DerivationArea;
use App\Models\Intention;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class IntentionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('intentions.index', [
            'intentions' => Intention::with('derivationArea')->orderByDesc('priority')->paginate(15),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('intentions.create', [
            'intention' => new Intention([
                'is_active' => true,
                'default_action' => Intention::ACTION_RESPOND_AI,
                'minimum_confidence' => 0.60,
                'color' => '#6c757d',
                'priority' => 0,
            ]),
            'areas' => DerivationArea::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Intention::create($this->validated($request));

        return redirect()->route('intentions.index')->with('status', 'Intencion creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Intention $intention): View
    {
        return view('intentions.show', ['intention' => $intention->load('derivationArea')]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Intention $intention): View
    {
        return view('intentions.edit', [
            'intention' => $intention,
            'areas' => DerivationArea::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Intention $intention): RedirectResponse
    {
        $intention->update($this->validated($request, $intention));

        return redirect()->route('intentions.index')->with('status', 'Intencion actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Intention $intention): RedirectResponse
    {
        $intention->delete();

        return redirect()->route('intentions.index')->with('status', 'Intencion eliminada correctamente.');
    }

    private function validated(Request $request, ?Intention $intention = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('intentions', 'slug')->ignore($intention?->id)],
            'description' => ['nullable', 'string'],
            'color' => ['required', 'string', 'max:20'],
            'icon' => ['nullable', 'string', 'max:100'],
            'priority' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'default_action' => ['required', Rule::in([
                Intention::ACTION_RESPOND_AI,
                Intention::ACTION_DERIVE,
                Intention::ACTION_RESPOND_AND_DERIVE,
            ])],
            'derivation_area_id' => ['nullable', 'exists:derivation_areas,id'],
            'minimum_confidence' => ['required', 'numeric', 'min:0', 'max:1'],
            'specific_prompt' => ['nullable', 'string'],
            'keywords' => ['nullable', 'string'],
        ]) + ['is_active' => false];

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        return $data;
    }
}
