<?php

namespace Tests\Feature;

use App\Models\AiInteraction;
use App\Models\AiProviderSetting;
use App\Models\DerivationArea;
use App\Models\Intention;
use App\Services\AI\AiIntentClassifierService;
use App\Services\AI\AiProviderManager;
use App\Services\AI\GeminiProviderService;
use App\Services\KnowledgeBaseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_key_is_encrypted(): void
    {
        $provider = new AiProviderSetting([
            'name' => 'OpenAI',
            'provider' => 'openai',
            'model' => 'gpt-test',
        ]);

        $provider->setApiKey('sk-test-secret-1234');
        $provider->save();

        $this->assertNotSame('sk-test-secret-1234', $provider->api_key_encrypted);
        $this->assertSame('1234', $provider->api_key_last_four);
        $this->assertSame('sk-test-secret-1234', $provider->apiKey());
    }

    public function test_default_provider_is_selected(): void
    {
        AiProviderSetting::create(['name' => 'DeepSeek', 'provider' => 'deepseek', 'model' => 'deepseek-chat', 'is_active' => true, 'is_default' => false]);
        AiProviderSetting::create(['name' => 'OpenAI', 'provider' => 'openai', 'model' => 'gpt-test', 'is_active' => true, 'is_default' => true]);

        $this->assertSame('openai', app(AiProviderManager::class)->defaultSetting()?->provider);
    }

    public function test_classifier_falls_back_to_basic_detection_when_ai_is_disabled(): void
    {
        $this->seed();

        $result = app(AiIntentClassifierService::class)->classify('quiero invertir en acciones', 'automatico', false);

        $this->assertSame('Inversion', $result['intention']->name);
    }

    public function test_mandatory_derivation_rules(): void
    {
        $this->seed();

        $knowledge = app(KnowledgeBaseService::class);

        $this->assertSame('Gerencia Comercial', $knowledge->detectarIntencionBasica('quiero invertir')['derivation_area']->name);
        $this->assertSame('Soporte', $knowledge->detectarIntencionBasica('tengo un reclamo')['derivation_area']->name);
        $this->assertSame('Soporte', $knowledge->detectarIntencionBasica('quiero garantia')['derivation_area']->name);
    }

    public function test_ai_interaction_can_be_created(): void
    {
        $this->seed();

        $interaction = AiInteraction::create([
            'provider' => 'local_rules',
            'model' => 'rules',
            'question' => 'Pregunta',
            'response' => 'Respuesta',
            'intention_id' => Intention::where('slug', 'otros')->first()->id,
            'confidence' => 0.40,
            'action' => 'responder_ia',
            'derivation_area_id' => DerivationArea::first()?->id,
            'success' => true,
        ]);

        $this->assertDatabaseHas('ai_interactions', ['id' => $interaction->id, 'success' => true]);
    }

    public function test_gemini_provider_can_be_created_and_selected(): void
    {
        AiProviderSetting::create([
            'name' => 'Gemini Flash',
            'provider' => 'gemini',
            'model' => 'gemini-2.5-flash',
            'is_active' => true,
            'is_default' => true,
        ]);

        $this->assertInstanceOf(GeminiProviderService::class, app(AiProviderManager::class)->provider('gemini'));
    }

    public function test_gemini_fails_without_api_key(): void
    {
        $setting = AiProviderSetting::create([
            'name' => 'Gemini Flash',
            'provider' => 'gemini',
            'model' => 'gemini-2.5-flash',
            'is_active' => true,
        ]);

        $response = (new GeminiProviderService($setting))->generateResponse([
            ['role' => 'user', 'content' => 'hola'],
        ]);

        $this->assertFalse($response['success']);
        $this->assertSame('gemini', $response['provider']);
        $this->assertSame('API key de Gemini no configurada.', $response['error']);
    }

    public function test_gemini_interaction_can_be_registered(): void
    {
        $interaction = AiInteraction::create([
            'provider' => 'gemini',
            'model' => 'gemini-2.5-flash',
            'question' => 'Pregunta',
            'response' => 'Respuesta',
            'success' => false,
            'error_message' => 'API key vacia',
        ]);

        $this->assertDatabaseHas('ai_interactions', [
            'id' => $interaction->id,
            'provider' => 'gemini',
            'success' => false,
        ]);
    }
}
