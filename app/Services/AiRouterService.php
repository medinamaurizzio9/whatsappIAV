<?php

namespace App\Services;

use App\Models\DerivationArea;
use App\Models\InitialMenuOption;
use Illuminate\Support\Str;

class AiRouterService
{
    /**
     * @return array{response_type:string, message:string, derivation_area_id:int|null, derivation_area:?DerivationArea}
     */
    public function route(InitialMenuOption $option, string $message): array
    {
        $text = Str::lower(Str::ascii($message));
        $title = Str::lower(Str::ascii($option->title));

        $areaName = null;

        if ($option->action === InitialMenuOption::ACTION_DERIVATION) {
            $areaName = $option->derivationArea?->name ?? $this->areaFromOption($title);
        } elseif (str_contains($title, 'comprar') && $this->containsAny($text, ['comprar', 'pagar', 'precio', 'reserva'])) {
            $areaName = 'Ventas';
        } elseif (str_contains($title, 'trabajar') && $this->containsAny($text, ['registrarme', 'afiliarme', 'vender'])) {
            $areaName = 'Supervisor Comercial';
        } elseif (str_contains($title, 'invertir')) {
            $areaName = 'Gerencia Comercial';
        } elseif ((str_contains($title, 'consulta') || str_contains($title, 'compre')) && $this->containsAny($text, ['reclamo', 'problema', 'garantia', 'no recibi'])) {
            $areaName = 'Soporte';
        }

        if ($areaName) {
            $area = DerivationArea::query()
                ->where('is_active', true)
                ->where('name', $areaName)
                ->first();

            return [
                'response_type' => 'derivacion',
                'message' => $area
                    ? "Gracias por escribir a VIANKA GOLD MINING. Tu caso sera derivado al area {$area->name}. WhatsApp: {$area->whatsapp_number}."
                    : 'Gracias por escribir a VIANKA GOLD MINING. Tu caso sera derivado al area correspondiente.',
                'derivation_area_id' => $area?->id,
                'derivation_area' => $area,
            ];
        }

        return [
            'response_type' => 'ia_simulada',
            'message' => $this->simulatedAiMessage($option),
            'derivation_area_id' => null,
            'derivation_area' => null,
        ];
    }

    private function containsAny(string $text, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($text, $needle)) {
                return true;
            }
        }

        return false;
    }

    private function areaFromOption(string $title): ?string
    {
        return match (true) {
            str_contains($title, 'invertir') => 'Gerencia Comercial',
            str_contains($title, 'trabajar') => 'Supervisor Comercial',
            str_contains($title, 'comprar') => 'Ventas',
            str_contains($title, 'consulta') || str_contains($title, 'compre') => 'Soporte',
            default => null,
        };
    }

    private function simulatedAiMessage(InitialMenuOption $option): string
    {
        return match ($option->sort_order) {
            1 => 'IA simulada: puedo orientarte sobre estilos, materiales, disponibilidad y el siguiente paso para elegir una joya.',
            2 => 'IA simulada: puedo explicarte como funciona el modelo de trabajo con VIANKA GOLD MINING y que datos preparar.',
            3 => 'IA simulada: esta consulta requiere atencion comercial especializada para inversion.',
            4 => 'IA simulada: puedo revisar tu consulta post-compra y ayudarte con informacion general de seguimiento.',
            default => 'IA simulada: recibimos tu mensaje y te daremos una respuesta orientativa dentro de esta prueba interna.',
        };
    }
}
