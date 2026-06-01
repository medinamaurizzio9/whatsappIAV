<?php

namespace App\Services\Commercial;

use App\Models\Client;
use App\Models\DerivationArea;
use App\Models\Intention;
use App\Models\LeadAlert;
use App\Models\LeadEvent;
use App\Models\LeadPipeline;
use App\Models\LeadPipelineHistory;
use App\Models\LeadScore;
use Illuminate\Support\Str;

class CommercialIntentService
{
    public const STAGES = [
        'Nuevo',
        'Contactado',
        'Interesado',
        'Negociación',
        'En seguimiento',
        'Cerrado ganado',
        'Cerrado perdido',
    ];

    public function detect(string $message, ?Intention $intention = null): array
    {
        $text = Str::lower(Str::ascii($message));
        $event = null;
        $points = 0;

        $rules = [
            'Confirma compra' => [100, ['confirme compra', 'confirmo compra', 'compre', 'ya pague']],
            'Envía comprobante' => [70, ['comprobante', 'voucher', 'recibo de pago']],
            'Quiere comprar' => [50, ['quiero comprar', 'comprar', 'me interesa la joya']],
            'Solicita inversión' => [40, ['invertir', 'inversion', 'rentabilidad', 'acciones']],
            'Solicita afiliación' => [40, ['afiliarme', 'afiliacion', 'registrarme']],
            'Solicita llamada' => [25, ['llamame', 'llamada', 'me llaman']],
            'Solicita asesor' => [25, ['asesor', 'humano', 'persona']],
            'Consulta catálogo' => [15, ['catalogo', 'catalogo de joyas']],
            'Consulta precio' => [10, ['precio', 'cuanto cuesta', 'valor']],
            'Pregunta por promociones' => [10, ['promocion', 'descuento', 'oferta']],
            'Pregunta por sorteos' => [10, ['sorteo', 'premio', 'ticket']],
        ];

        foreach ($rules as $name => [$rulePoints, $needles]) {
            foreach ($needles as $needle) {
                if (str_contains($text, $needle)) {
                    $event = $name;
                    $points = $rulePoints;
                    break 2;
                }
            }
        }

        if (! $event && $intention) {
            [$event, $points] = match ($intention->slug) {
                'compra-de-joya' => ['Quiere comprar', 50],
                'consulta-de-precios' => ['Consulta precio', 10],
                'promociones' => ['Pregunta por promociones', 10],
                'sorteos' => ['Pregunta por sorteos', 10],
                'inversion' => ['Solicita inversión', 40],
                'afiliacion', 'trabajo' => ['Solicita afiliación', 40],
                'reclamo', 'garantia' => ['Soporte', 0],
                default => ['Consulta general', 1],
            };
        }

        return [
            'commercial_intent' => $event ?: 'Consulta general',
            'points' => $points ?: 1,
            'category' => $this->commercialCategory($intention),
        ];
    }

    public function registerEvent(Client $client, string $message, ?Intention $intention = null): LeadEvent
    {
        $detected = $this->detect($message, $intention);
        $area = $this->areaFor($intention);

        $event = LeadEvent::create([
            'client_id' => $client->id,
            'intention_id' => $intention?->id,
            'derivation_area_id' => $area?->id,
            'evento' => $detected['commercial_intent'],
            'puntos' => $detected['points'],
            'descripcion' => $message,
        ]);

        $score = $this->recalculateScore($client);
        $this->ensurePipeline($client, $area);
        $this->createAlerts($client, $event, $score, $intention);

        return $event;
    }

    public function recalculateScore(Client $client): LeadScore
    {
        $score = (int) $client->leadEvents()->sum('puntos');

        return LeadScore::updateOrCreate(
            ['client_id' => $client->id],
            ['score' => $score, 'categoria' => $this->categoryForScore($score), 'estado' => 'activo']
        );
    }

    public function moveStage(Client $client, string $stage, ?int $userId = null, ?string $notes = null): LeadPipeline
    {
        $pipeline = $client->leadPipeline ?: $this->ensurePipeline($client);
        $from = $pipeline->stage;
        $pipeline->update(['stage' => $stage, 'last_moved_at' => now()]);
        LeadPipelineHistory::create(['client_id' => $client->id, 'user_id' => $userId, 'from_stage' => $from, 'to_stage' => $stage, 'notes' => $notes]);

        return $pipeline;
    }

    public function probabilities(Client $client): array
    {
        $score = $client->leadScore?->score ?? 0;
        $events = $client->leadEvents;

        return [
            'compra' => min(100, $score + ($events->whereIn('evento', ['Quiere comprar', 'Consulta precio', 'Consulta catálogo'])->sum('puntos') / 2)),
            'afiliacion' => min(100, $events->where('evento', 'Solicita afiliación')->sum('puntos') + ($score * 0.25)),
            'inversion' => min(100, $events->where('evento', 'Solicita inversión')->sum('puntos') + ($score * 0.25)),
        ];
    }

    public function categoryForScore(int $score): string
    {
        return match (true) {
            $score >= 90 => 'Muy Caliente',
            $score >= 60 => 'Caliente',
            $score >= 30 => 'Tibio',
            default => 'Frio',
        };
    }

    private function ensurePipeline(Client $client, ?DerivationArea $area = null): LeadPipeline
    {
        return LeadPipeline::firstOrCreate(
            ['client_id' => $client->id],
            ['stage' => 'Nuevo', 'assigned_area_id' => $area?->id, 'last_moved_at' => now()]
        );
    }

    private function createAlerts(Client $client, LeadEvent $event, LeadScore $score, ?Intention $intention): void
    {
        if ($score->score >= 80) {
            LeadAlert::firstOrCreate(
                ['client_id' => $client->id, 'type' => 'score_80', 'status' => 'pendiente'],
                ['intention_id' => $intention?->id, 'title' => 'Lead supera 80 puntos', 'message' => "{$client->name} tiene {$score->score} puntos."]
            );
        }

        if (in_array($event->evento, ['Quiere comprar', 'Solicita inversión', 'Solicita afiliación'], true)) {
            LeadAlert::create([
                'client_id' => $client->id,
                'intention_id' => $intention?->id,
                'type' => Str::slug($event->evento, '_'),
                'title' => $event->evento,
                'message' => "{$client->name}: {$event->descripcion}",
            ]);
        }
    }

    private function areaFor(?Intention $intention): ?DerivationArea
    {
        return match ($intention?->slug) {
            'compra-de-joya', 'consulta-de-precios', 'pagos', 'hablar-con-asesor' => DerivationArea::where('name', 'Ventas')->first(),
            'afiliacion', 'trabajo' => DerivationArea::where('name', 'Supervisor Comercial')->first(),
            'inversion' => DerivationArea::where('name', 'Gerencia Comercial')->first(),
            'reclamo', 'garantia', 'estado-de-pedido' => DerivationArea::where('name', 'Soporte')->first(),
            default => $intention?->derivationArea,
        };
    }

    private function commercialCategory(?Intention $intention): string
    {
        return match ($intention?->slug) {
            'compra-de-joya', 'consulta-de-precios', 'pagos' => 'Compra',
            'afiliacion', 'trabajo' => 'Afiliación',
            'inversion' => 'Inversión',
            'reclamo', 'garantia' => 'Soporte',
            default => 'General',
        };
    }
}
