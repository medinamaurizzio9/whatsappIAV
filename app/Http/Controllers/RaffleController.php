<?php

namespace App\Http\Controllers;

use App\Models\Raffle;
use App\Models\Intention;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RaffleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('knowledge.raffles.index', ['raffles' => Raffle::with('intentions')->latest()->paginate(10)]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('knowledge.raffles.create', ['raffle' => new Raffle(['is_active' => true]), 'intentions' => Intention::where('is_active', true)->orderByDesc('priority')->get()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $intentions = $data['intentions'] ?? [];
        unset($data['intentions']);
        $raffle = Raffle::create($data);
        $raffle->intentions()->sync($intentions);

        return redirect()->route('raffles.index')->with('status', 'Sorteo creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Raffle $raffle): View
    {
        return view('knowledge.raffles.show', ['raffle' => $raffle->load('intentions')]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Raffle $raffle): View
    {
        return view('knowledge.raffles.edit', ['raffle' => $raffle, 'intentions' => Intention::where('is_active', true)->orderByDesc('priority')->get()]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Raffle $raffle): RedirectResponse
    {
        $data = $this->validated($request);
        $intentions = $data['intentions'] ?? [];
        unset($data['intentions']);
        $raffle->update($data);
        $raffle->intentions()->sync($intentions);

        return redirect()->route('raffles.index')->with('status', 'Sorteo actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Raffle $raffle): RedirectResponse
    {
        $raffle->delete();

        return redirect()->route('raffles.index')->with('status', 'Sorteo eliminado correctamente.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'prizes' => ['nullable', 'string'],
            'raffle_date' => ['nullable', 'date'],
            'rules' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'intentions' => ['nullable', 'array'],
            'intentions.*' => ['exists:intentions,id'],
        ]) + ['is_active' => false];
    }
}
