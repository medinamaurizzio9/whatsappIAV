<?php

namespace App\Http\Controllers;

use App\Models\DerivationArea;
use App\Models\InitialMenuOption;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InitialMenuOptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('menu-options.index', [
            'options' => InitialMenuOption::with('derivationArea')->orderBy('sort_order')->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('menu-options.create', [
            'option' => new InitialMenuOption(['is_active' => true, 'action' => InitialMenuOption::ACTION_IA]),
            'areas' => DerivationArea::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        InitialMenuOption::create($this->validated($request));

        return redirect()->route('menu-options.index')->with('status', 'Opcion creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(InitialMenuOption $menuOption)
    {
        return view('menu-options.show', ['option' => $menuOption->load('derivationArea')]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InitialMenuOption $menuOption): View
    {
        return view('menu-options.edit', [
            'option' => $menuOption,
            'areas' => DerivationArea::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InitialMenuOption $menuOption): RedirectResponse
    {
        $menuOption->update($this->validated($request));

        return redirect()->route('menu-options.index')->with('status', 'Opcion actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InitialMenuOption $menuOption): RedirectResponse
    {
        $menuOption->delete();

        return redirect()->route('menu-options.index')->with('status', 'Opcion eliminada correctamente.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'action' => ['required', Rule::in([InitialMenuOption::ACTION_IA, InitialMenuOption::ACTION_DERIVATION])],
            'derivation_area_id' => ['nullable', 'exists:derivation_areas,id'],
        ]) + ['is_active' => false];
    }
}
