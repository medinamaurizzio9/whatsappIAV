<?php

namespace App\Http\Controllers;

use App\Models\SimulatedConversation;
use Illuminate\View\View;

class SimulatedConversationController extends Controller
{
    public function index(): View
    {
        return view('conversations.index', [
            'conversations' => SimulatedConversation::with(['client', 'derivationArea', 'initialMenuOption'])
                ->latest('responded_at')
                ->paginate(15),
        ]);
    }

    public function show(SimulatedConversation $conversation): View
    {
        return view('conversations.show', [
            'conversation' => $conversation->load(['client', 'derivationArea', 'initialMenuOption']),
        ]);
    }
}
