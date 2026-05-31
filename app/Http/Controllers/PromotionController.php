<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use App\Models\Intention;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('knowledge.promotions.index', ['promotions' => Promotion::with('intentions')->latest()->paginate(10)]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('knowledge.promotions.create', ['promotion' => new Promotion(['is_active' => true]), 'intentions' => Intention::where('is_active', true)->orderByDesc('priority')->get()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $intentions = $data['intentions'] ?? [];
        unset($data['intentions']);
        $promotion = Promotion::create($data);
        $promotion->intentions()->sync($intentions);

        return redirect()->route('promotions.index')->with('status', 'Promocion creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Promotion $promotion): View
    {
        return view('knowledge.promotions.show', ['promotion' => $promotion->load('intentions')]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Promotion $promotion): View
    {
        return view('knowledge.promotions.edit', ['promotion' => $promotion, 'intentions' => Intention::where('is_active', true)->orderByDesc('priority')->get()]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Promotion $promotion): RedirectResponse
    {
        $data = $this->validated($request);
        $intentions = $data['intentions'] ?? [];
        unset($data['intentions']);
        $promotion->update($data);
        $promotion->intentions()->sync($intentions);

        return redirect()->route('promotions.index')->with('status', 'Promocion actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Promotion $promotion): RedirectResponse
    {
        $promotion->delete();

        return redirect()->route('promotions.index')->with('status', 'Promocion eliminada correctamente.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['nullable', 'boolean'],
            'intentions' => ['nullable', 'array'],
            'intentions.*' => ['exists:intentions,id'],
        ]) + ['is_active' => false];
    }
}
