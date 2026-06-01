<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeFeedback;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KnowledgeFeedbackController extends Controller
{
    public function index(): View
    {
        return view('knowledge.feedback.index', [
            'feedback' => KnowledgeFeedback::with(['user', 'intention'])->latest()->paginate(15),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'question' => ['required', 'string'],
            'generated_answer' => ['nullable', 'string'],
            'correct_answer' => ['nullable', 'string'],
            'intention_id' => ['nullable', 'exists:intentions,id'],
            'provider' => ['nullable', 'string', 'max:100'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string'],
        ]);

        KnowledgeFeedback::create($data + ['user_id' => $request->user()->id]);

        return back()->with('status', 'Feedback registrado correctamente.');
    }
}
