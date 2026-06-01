<?php

namespace App\Http\Controllers;

use App\Models\AiInteraction;
use App\Models\DerivationArea;
use App\Models\Intention;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AiInteractionController extends Controller
{
    public function index(Request $request): View
    {
        return view('ai.interactions.index', [
            'interactions' => AiInteraction::with(['user', 'intention', 'derivationArea'])
                ->when($request->provider, fn ($query, $value) => $query->where('provider', $value))
                ->when($request->intention_id, fn ($query, $value) => $query->where('intention_id', $value))
                ->when($request->action, fn ($query, $value) => $query->where('action', $value))
                ->when($request->derivation_area_id, fn ($query, $value) => $query->where('derivation_area_id', $value))
                ->when($request->filled('success'), fn ($query) => $query->where('success', request('success')))
                ->when($request->from, fn ($query, $value) => $query->whereDate('created_at', '>=', $value))
                ->when($request->to, fn ($query, $value) => $query->whereDate('created_at', '<=', $value))
                ->latest()
                ->paginate(15)
                ->withQueryString(),
            'intentions' => Intention::where('is_active', true)->orderBy('name')->get(),
            'areas' => DerivationArea::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function show(AiInteraction $interaction): View
    {
        return view('ai.interactions.show', ['interaction' => $interaction->load(['user', 'intention', 'derivationArea'])]);
    }
}
