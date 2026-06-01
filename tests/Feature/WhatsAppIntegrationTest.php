<?php

namespace Tests\Feature;

use App\Models\DerivationArea;
use App\Models\User;
use App\Models\WhatsAppContact;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Models\WhatsAppOutboundLog;
use App\Models\WhatsAppSetting;
use App\Services\WhatsApp\WhatsAppCloudService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhatsAppIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_verification(): void
    {
        $this->createSetting();

        $this->get('/webhook/whatsapp?hub.mode=subscribe&hub.verify_token=verify-demo&hub.challenge=abc123')
            ->assertOk()
            ->assertSee('abc123');
    }

    public function test_receives_message_and_creates_contact_and_conversation(): void
    {
        $this->seed();
        $this->createSetting();
        Http::fake(['graph.facebook.com/*' => Http::response(['messages' => [['id' => 'wamid.out']]], 200)]);

        $this->postJson('/webhook/whatsapp', $this->payload('hola'))->assertOk();

        $this->assertDatabaseHas('whatsapp_contacts', ['wa_id' => '59170000000']);
        $this->assertDatabaseHas('whatsapp_conversations', ['status' => 'open']);
        $this->assertDatabaseHas('whatsapp_messages', ['direction' => 'inbound', 'body' => 'hola']);
    }

    public function test_mocked_send_text(): void
    {
        $setting = $this->createSetting();
        Http::fake(['graph.facebook.com/*' => Http::response(['messages' => [['id' => 'wamid.out']]], 200)]);

        $result = (new WhatsAppCloudService($setting))->sendText('59170000000', 'Hola');

        $this->assertTrue($result['success']);
        $this->assertDatabaseHas('whatsapp_outbound_logs', ['to_phone' => '59170000000', 'success' => true]);
    }

    public function test_manual_mode_stores_ai_suggestion_without_sending(): void
    {
        $this->seed();
        $this->createSetting('manual');

        $this->postJson('/webhook/whatsapp', $this->payload('quiero saber precio'))->assertOk();

        $this->assertDatabaseHas('whatsapp_messages', ['direction' => 'outbound', 'status' => 'suggested']);
        $this->assertSame(0, WhatsAppOutboundLog::count());
    }

    public function test_supervised_mode_requires_approval(): void
    {
        $this->seed();
        $this->createSetting('supervisado');

        $this->postJson('/webhook/whatsapp', $this->payload('quiero saber precio'))->assertOk();

        $this->assertDatabaseHas('whatsapp_messages', [
            'direction' => 'outbound',
            'status' => 'pending_approval',
            'requires_approval' => true,
        ]);
    }

    public function test_automatic_mode_can_send_menu_response(): void
    {
        $this->seed();
        $this->createSetting('automatico');
        Http::fake(['graph.facebook.com/*' => Http::response(['messages' => [['id' => 'wamid.out']]], 200)]);

        $this->postJson('/webhook/whatsapp', $this->payload('menu'))->assertOk();

        $this->assertDatabaseHas('whatsapp_messages', ['direction' => 'outbound', 'status' => 'sent']);
        $this->assertDatabaseHas('whatsapp_outbound_logs', ['to_phone' => '59170000000', 'success' => true]);
    }

    public function test_investment_is_derived_to_commercial_management(): void
    {
        $this->assertDerivedTo('quiero invertir en acciones', 'Gerencia Comercial');
    }

    public function test_claim_is_derived_to_support(): void
    {
        $this->assertDerivedTo('tengo un reclamo porque no recibi mi compra', 'Soporte');
    }

    public function test_warranty_is_derived_to_support(): void
    {
        $this->assertDerivedTo('necesito garantia o cambio', 'Soporte');
    }

    public function test_inbox_requires_authentication(): void
    {
        $this->get('/whatsapp/inbox')->assertRedirect('/login');
    }

    public function test_authenticated_user_can_open_inbox(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'operador']));

        $this->get('/whatsapp/inbox')->assertOk();
    }

    private function assertDerivedTo(string $message, string $areaName): void
    {
        $this->seed();
        $this->createSetting('manual');

        $this->postJson('/webhook/whatsapp', $this->payload($message))->assertOk();

        $area = DerivationArea::where('name', $areaName)->firstOrFail();
        $this->assertDatabaseHas('whatsapp_conversations', ['derivation_area_id' => $area->id]);
        $this->assertDatabaseHas('whatsapp_messages', ['direction' => 'inbound', 'derivation_area_id' => $area->id]);
    }

    private function createSetting(string $mode = 'manual'): WhatsAppSetting
    {
        $setting = WhatsAppSetting::create([
            'business_account_id' => '123',
            'phone_number_id' => '456',
            'display_phone_number' => '+59170000001',
            'verify_token' => 'verify-demo',
            'api_version' => 'v21.0',
            'attention_mode' => $mode,
            'is_active' => true,
        ]);
        $setting->setAccessToken('EAAB-test-token-1234');
        $setting->save();

        return $setting;
    }

    private function payload(string $text, string $type = 'text'): array
    {
        $message = [
            'from' => '59170000000',
            'id' => 'wamid.'.md5($text),
            'timestamp' => (string) now()->timestamp,
            'type' => $type,
        ];

        if ($type === 'text') {
            $message['text'] = ['body' => $text];
        }

        return [
            'object' => 'whatsapp_business_account',
            'entry' => [[
                'id' => '123',
                'changes' => [[
                    'value' => [
                        'messaging_product' => 'whatsapp',
                        'metadata' => ['display_phone_number' => '+59170000001', 'phone_number_id' => '456'],
                        'contacts' => [[
                            'profile' => ['name' => 'Cliente WhatsApp'],
                            'wa_id' => '59170000000',
                        ]],
                        'messages' => [$message],
                    ],
                    'field' => 'messages',
                ]],
            ]],
        ];
    }
}
