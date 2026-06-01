<?php

namespace App\Http\Controllers;

use App\Models\Intention;
use App\Models\UnansweredQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UnansweredQuestionController extends Controller
{
    public function index(): View
    {
        return view('knowledge.unanswered.index', [
            'questions' => UnansweredQuestion::with('intention')
                ->when(request('status'), fn ($query, $status) => $query->where('status', $status))
                ->latest()
                ->paginate(15)
                ->withQueryString(),
        ]);
    }

    public function show(UnansweredQuestion $unansweredQuestion): View
    {
        return view('knowledge.unanswered.show', ['question' => $unansweredQuestion->load('intention')]);
    }

    public function edit(UnansweredQuestion $unansweredQuestion): View
    {
        return view('knowledge.unanswered.edit', [
            'question' => $unansweredQuestion,
            'intentions' => Intention::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, UnansweredQuestion $unansweredQuestion): RedirectResponse
    {
        $unansweredQuestion->update($request->validate([
            'intention_id' => ['nullable', 'exists:intentions,id'],
            'question' => ['required', 'string'],
            'reason' => ['nullable', 'string'],
            'status' => ['required', 'in:pendiente,corregida,descartada'],
        ]));

        return redirect()->route('unanswered-questions.index')->with('status', 'Pregunta pendiente actualizada.');
    }

    public function destroy(UnansweredQuestion $unansweredQuestion): RedirectResponse
    {
        $unansweredQuestion->delete();

        return redirect()->route('unanswered-questions.index')->with('status', 'Pregunta eliminada.');
    }
}
