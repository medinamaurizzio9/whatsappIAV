<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Intention;
use App\Models\LeadAlert;
use App\Services\Commercial\CommercialIntentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommercialCrmTest extends TestCase
{
    use RefreshDatabase;

    public function test_lead_event_updates_score_and_category(): void
    {
        $this->seed();
        $client = Client::first();
        $intention = Intention::where('slug', 'compra-de-joya')->first();

        app(CommercialIntentService::class)->registerEvent($client, 'quiero comprar una joya', $intention);

        $this->assertSame(50, $client->fresh()->leadScore->score);
        $this->assertSame('Tibio', $client->fresh()->leadScore->categoria);
    }

    public function test_hot_lead_creates_alert(): void
    {
        $this->seed();
        $client = Client::first();
        $service = app(CommercialIntentService::class);
        $intention = Intention::where('slug', 'compra-de-joya')->first();

        $service->registerEvent($client, 'envio comprobante', $intention);
        $service->registerEvent($client, 'quiero comprar', $intention);

        $this->assertDatabaseHas('lead_alerts', ['client_id' => $client->id, 'type' => 'score_80']);
    }

    public function test_pipeline_can_move_stage(): void
    {
        $this->seed();
        $client = Client::first();

        app(CommercialIntentService::class)->moveStage($client, 'Negociación');

        $this->assertSame('Negociación', $client->fresh()->leadPipeline->stage);
    }
}
