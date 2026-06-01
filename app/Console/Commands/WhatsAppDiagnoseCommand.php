<?php

namespace App\Console\Commands;

use App\Models\WhatsAppSetting;
use Illuminate\Console\Command;

class WhatsAppDiagnoseCommand extends Command
{
    protected $signature = 'whatsapp:diagnose';

    protected $description = 'Revisa la configuracion local de WhatsApp Cloud API sin exponer secretos.';

    public function handle(): int
    {
        $setting = WhatsAppSetting::active();

        if (! $setting) {
            $this->error('No hay configuracion de WhatsApp.');
            return self::FAILURE;
        }

        $this->info('Configuracion WhatsApp');
        $this->line('Activo: '.($setting->is_active ? 'si' : 'no'));
        $this->line('Phone Number ID: '.($setting->phone_number_id ?: '-'));
        $this->line('Business Account ID: '.($setting->business_account_id ?: '-'));
        $this->line('Telefono: '.($setting->display_phone_number ?: '-'));
        $this->line('API version: '.$setting->api_version);
        $this->line('Modo: '.$setting->attention_mode);
        $this->line('Webhook URL: '.($setting->webhook_url ?: url('/webhook/whatsapp')));
        $this->line('Access token: '.$setting->maskedAccessToken());
        $this->line('App secret: '.($setting->app_secret_encrypted ? 'configurado' : 'no configurado'));

        return self::SUCCESS;
    }
}
