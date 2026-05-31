<?php

namespace App\Http\Controllers;

use App\Models\GeneralSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GeneralSettingController extends Controller
{
    public function edit(): View
    {
        return view('settings.edit', [
            'setting' => GeneralSetting::query()->firstOrCreate([], [
                'company_name' => 'VIANKA GOLD MINING',
                'welcome_message' => 'Bienvenido a VIANKA GOLD MINING. Selecciona una opcion para ayudarte.',
            ]),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $setting = GeneralSetting::query()->firstOrCreate([]);

        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'main_phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'business_hours' => ['nullable', 'string', 'max:255'],
            'welcome_message' => ['nullable', 'string'],
        ]);

        if ($request->hasFile('logo')) {
            if ($setting->logo_path) {
                Storage::disk('public')->delete($setting->logo_path);
            }

            $data['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        unset($data['logo']);

        $setting->update($data);

        return redirect()->route('settings.edit')->with('status', 'Configuracion actualizada correctamente.');
    }
}
