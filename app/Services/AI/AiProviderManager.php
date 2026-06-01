<?php

namespace App\Services\AI;

use App\Contracts\AiProviderInterface;
use App\Models\AiProviderSetting;
use RuntimeException;

class AiProviderManager
{
    public function defaultSetting(?string $provider = null): ?AiProviderSetting
    {
        return AiProviderSetting::query()
            ->where('is_active', true)
            ->when($provider && $provider !== 'automatico', fn ($query) => $query->where('provider', $provider))
            ->orderByDesc('is_default')
            ->latest()
            ->first();
    }

    public function provider(?string $provider = null): AiProviderInterface
    {
        $setting = $this->defaultSetting($provider);

        if (! $setting) {
            throw new RuntimeException('No hay proveedor IA activo configurado.');
        }

        return match ($setting->provider) {
            'openai' => new OpenAiProviderService($setting),
            'deepseek' => new DeepSeekProviderService($setting),
            default => throw new RuntimeException('Proveedor IA no soportado.'),
        };
    }

    public function settingsForComparison(): array
    {
        return [
            'openai' => $this->defaultSetting('openai'),
            'deepseek' => $this->defaultSetting('deepseek'),
        ];
    }
}
