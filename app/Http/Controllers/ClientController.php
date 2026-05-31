<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('clients.index', [
            'clients' => Client::query()->latest()->paginate(10),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_unless(request()->user()?->isAdmin(), 403);

        return view('clients.create', ['client' => new Client(['type' => 'prospecto'])]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        Client::create($this->validated($request));

        return redirect()->route('clients.index')->with('status', 'Cliente creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client): View
    {
        return view('clients.show', [
            'client' => $client->load(['simulatedConversations.derivationArea', 'simulatedConversations.initialMenuOption']),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client): View
    {
        abort_unless(request()->user()?->isAdmin(), 403);

        return view('clients.edit', ['client' => $client]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $client->update($this->validated($request));

        return redirect()->route('clients.index')->with('status', 'Cliente actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Client $client): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $client->delete();

        return redirect()->route('clients.index')->with('status', 'Cliente eliminado correctamente.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'city' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'in:prospecto,comprador,inversionista,trabajador/interesado'],
            'observations' => ['nullable', 'string'],
        ]);
    }
}
