<?php

namespace App\Http\Controllers;

use App\Models\DerivationArea;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DerivationAreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('derivation-areas.index', [
            'areas' => DerivationArea::query()->latest()->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('derivation-areas.create', [
            'area' => new DerivationArea(['is_active' => true]),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        DerivationArea::create($this->validated($request));

        return redirect()->route('derivation-areas.index')->with('status', 'Area creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DerivationArea $derivationArea)
    {
        return view('derivation-areas.show', ['area' => $derivationArea]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DerivationArea $derivationArea): View
    {
        return view('derivation-areas.edit', ['area' => $derivationArea]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DerivationArea $derivationArea): RedirectResponse
    {
        $derivationArea->update($this->validated($request));

        return redirect()->route('derivation-areas.index')->with('status', 'Area actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DerivationArea $derivationArea): RedirectResponse
    {
        $derivationArea->delete();

        return redirect()->route('derivation-areas.index')->with('status', 'Area eliminada correctamente.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'whatsapp_number' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => false];
    }
}
