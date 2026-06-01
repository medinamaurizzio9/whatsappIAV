<?php

namespace App\Http\Controllers;

use App\Models\WhatsAppSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WhatsAppSettingsController extends Controller
{
    public function edit(): View
    {
        return view('whatsapp.settings', [
            'setting' => WhatsAppSetting::latest()->first() ?? new WhatsAppSetting([
                'api_version' => 'v21.0',
                'attention_mode' => WhatsAppSetting::MODE_MANUAL,
                'verify_token' => bin2hex(random_bytes(16)),
            ]),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'business_account_id' => ['nullable', 'string', 'max:255'],
            'phone_number_id' => ['nullable', 'string', 'max:255'],
            'display_phone_number' => ['nullable', 'string', 'max:60'],
            'access_token' => ['nullable', 'string'],
            'verify_token' => ['required', 'string', 'max:255'],
            'app_secret' => ['nullable', 'string'],
            'webhook_url' => ['nullable', 'url', 'max:255'],
            'api_version' => ['required', 'string', 'max:20'],
            'attention_mode' => ['required', Rule::in([WhatsAppSetting::MODE_MANUAL, WhatsAppSetting::MODE_SUPERVISED, WhatsAppSetting::MODE_AUTOMATIC])],
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => false];

        $token = $data['access_token'] ?? null;
        $secret = $data['app_secret'] ?? null;
        unset($data['access_token'], $data['app_secret']);

        $setting = WhatsAppSetting::latest()->first() ?? new WhatsAppSetting();
        $setting->fill($data);
        $setting->setAccessToken($token);
        $setting->setAppSecret($secret);
        $setting->save();

        return redirect()->route('whatsapp.settings')->with('status', 'Configuracion de WhatsApp actualizada.');
    }
}
