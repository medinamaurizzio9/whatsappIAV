<?php

namespace App\Http\Controllers;

use App\Models\AiProviderSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AiProviderSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('ai.providers.index', ['providers' => AiProviderSetting::latest()->paginate(10)]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('ai.providers.create', ['provider' => new AiProviderSetting(['is_active' => true, 'temperature' => 0.30, 'max_tokens' => 800, 'timeout_seconds' => 30])]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, true);
        $apiKey = $data['api_key'] ?? null;
        unset($data['api_key']);
        $provider = new AiProviderSetting($data);
        $provider->setApiKey($apiKey);
        $provider->save();
        $this->ensureSingleDefault($provider);

        return redirect()->route('ai-providers.index')->with('status', 'Proveedor IA creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AiProviderSetting $aiProvider): View
    {
        return view('ai.providers.show', ['provider' => $aiProvider]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AiProviderSetting $aiProvider): View
    {
        return view('ai.providers.edit', ['provider' => $aiProvider]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AiProviderSetting $aiProvider): RedirectResponse
    {
        $data = $this->validated($request, false);
        $apiKey = $data['api_key'] ?? null;
        unset($data['api_key']);
        $aiProvider->fill($data);
        $aiProvider->setApiKey($apiKey);
        $aiProvider->save();
        $this->ensureSingleDefault($aiProvider);

        return redirect()->route('ai-providers.index')->with('status', 'Proveedor IA actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AiProviderSetting $aiProvider): RedirectResponse
    {
        $aiProvider->delete();

        return redirect()->route('ai-providers.index')->with('status', 'Proveedor IA eliminado correctamente.');
    }

    private function validated(Request $request, bool $creating): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'provider' => ['required', Rule::in(['openai', 'deepseek', 'gemini'])],
            'model' => ['required', 'string', 'max:255'],
            'api_key' => [$creating ? 'required' : 'nullable', 'string'],
            'endpoint' => ['nullable', 'url', 'max:255'],
            'temperature' => ['required', 'numeric', 'min:0', 'max:2'],
            'max_tokens' => ['required', 'integer', 'min:1', 'max:200000'],
            'timeout_seconds' => ['required', 'integer', 'min:5', 'max:120'],
            'is_active' => ['nullable', 'boolean'],
            'is_default' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ]) + ['is_active' => false, 'is_default' => false];
    }

    private function ensureSingleDefault(AiProviderSetting $provider): void
    {
        if ($provider->is_default) {
            AiProviderSetting::whereKeyNot($provider->id)->update(['is_default' => false]);
        }
    }
}
