<?php

namespace App\Services\WhatsApp;

use App\Models\Client;
use App\Models\DerivationArea;
use App\Models\Intention;
use App\Models\WhatsAppContact;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMediaFile;
use App\Models\WhatsAppMessage;
use App\Models\WhatsAppSetting;
use App\Services\AI\AiKnowledgeResponderService;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class WhatsAppInboundProcessor
{
    public function __construct(
        private readonly AiKnowledgeResponderService $responder,
        private readonly WhatsAppCloudService $cloud,
    ) {
    }

    public function process(array $payload): array
    {
        $processed = [];

        foreach (Arr::get($payload, 'entry', []) as $entry) {
            foreach (Arr::get($entry, 'changes', []) as $change) {
                $value = $change['value'] ?? [];
                $contacts = collect($value['contacts'] ?? [])->keyBy('wa_id');

                foreach ($value['messages'] ?? [] as $incoming) {
                    $processed[] = $this->processMessage($incoming, $contacts->get($incoming['from'] ?? '') ?? []);
                }
            }
        }

        return $processed;
    }

    private function processMessage(array $incoming, array $profile): array
    {
        $from = (string) ($incoming['from'] ?? '');
        $type = (string) ($incoming['type'] ?? 'text');
        $body = $this->extractBody($incoming, $type);
        $setting = WhatsAppSetting::active();

        $contact = WhatsAppContact::updateOrCreate(
            ['wa_id' => $from],
            [
                'phone' => $from,
                'name' => data_get($profile, 'profile.name'),
                'client_id' => Client::where('phone', $from)->orWhere('phone', '+'.$from)->first()?->id,
                'profile_json' => $profile,
                'last_seen_at' => now(),
            ]
        );

        $conversation = WhatsAppConversation::firstOrCreate(
            ['whatsapp_contact_id' => $contact->id, 'status' => 'open'],
            ['client_id' => $contact->client_id, 'attention_mode' => $setting?->attention_mode ?? WhatsAppSetting::MODE_MANUAL]
        );

        $conversation->update([
            'last_message_preview' => Str::limit($body ?: strtoupper($type), 120),
            'last_message_at' => now(),
        ]);

        $message = WhatsAppMessage::create([
            'whatsapp_conversation_id' => $conversation->id,
            'direction' => 'inbound',
            'message_id' => $incoming['id'] ?? null,
            'type' => $type,
            'body' => $body,
            'payload_json' => $incoming,
        ]);

        if (in_array($type, ['image', 'audio', 'video', 'document'], true)) {
            $media = $incoming[$type] ?? [];
            WhatsAppMediaFile::create([
                'whatsapp_message_id' => $message->id,
                'media_id' => (string) ($media['id'] ?? ''),
                'type' => $type,
                'mime_type' => $media['mime_type'] ?? null,
                'filename' => $media['filename'] ?? null,
                'sha256' => $media['sha256'] ?? null,
                'payload_json' => $media,
            ]);
        }

        if ($type === 'text' && $this->isMenuRequest($body)) {
            return $this->replyOrSuggest($conversation, $message, $this->initialMenu(), null, true);
        }

        if ($type !== 'text') {
            $suggestion = 'Recibimos tu archivo o contenido multimedia. Un operador lo revisara desde la bandeja.';
            return $this->replyOrSuggest($conversation, $message, $suggestion, null, false);
        }

        $result = $this->responder->respond($body, ['provider' => 'automatico', 'use_knowledge' => true, 'use_intent' => true]);
        $area = $this->forcedArea($body, $result['intention'] ?? null) ?: ($result['derivation_area'] ?? null);
        $action = $area ? Intention::ACTION_DERIVE : ($result['recommended_action'] ?? Intention::ACTION_RESPOND_AI);

        $message->update([
            'intention_id' => $result['intention']?->id ?? null,
            'confidence' => $result['confidence'] ?? null,
            'recommended_action' => $action,
            'derivation_area_id' => $area?->id,
            'ai_result_json' => $this->compactAiResult($result),
        ]);

        if ($area) {
            $conversation->update(['derivation_area_id' => $area->id]);
        }

        return $this->handleMode($conversation, $message, $result, $area, $action);
    }

    private function handleMode(WhatsAppConversation $conversation, WhatsAppMessage $message, array $result, ?DerivationArea $area, string $action): array
    {
        $mode = $conversation->attention_mode;
        $content = $this->responseText($result, $area);
        $canAutoRespond = $this->canAutoRespond($result, $area, $action);

        if ($mode === WhatsAppSetting::MODE_AUTOMATIC && $canAutoRespond) {
            return $this->replyOrSuggest($conversation, $message, $content, $result, true);
        }

        $outbound = WhatsAppMessage::create([
            'whatsapp_conversation_id' => $conversation->id,
            'direction' => 'outbound',
            'type' => 'text',
            'body' => $content,
            'status' => $mode === WhatsAppSetting::MODE_SUPERVISED ? 'pending_approval' : 'suggested',
            'intention_id' => $result['intention']?->id ?? null,
            'confidence' => $result['confidence'] ?? null,
            'recommended_action' => $action,
            'derivation_area_id' => $area?->id,
            'requires_approval' => $mode === WhatsAppSetting::MODE_SUPERVISED,
            'ai_result_json' => $this->compactAiResult($result),
        ]);

        return ['mode' => $mode, 'conversation' => $conversation, 'message' => $message, 'suggestion' => $outbound];
    }

    private function replyOrSuggest(WhatsAppConversation $conversation, WhatsAppMessage $message, string $content, ?array $result, bool $send): array
    {
        $outbound = WhatsAppMessage::create([
            'whatsapp_conversation_id' => $conversation->id,
            'direction' => 'outbound',
            'type' => 'text',
            'body' => $content,
            'status' => $send ? 'sent' : 'suggested',
            'intention_id' => $result['intention']?->id ?? null,
            'confidence' => $result['confidence'] ?? null,
            'recommended_action' => $result['recommended_action'] ?? null,
            'derivation_area_id' => $result['derivation_area']?->id ?? null,
            'requires_approval' => false,
            'ai_result_json' => $result ? $this->compactAiResult($result) : null,
            'sent_at' => $send ? now() : null,
        ]);

        if ($send) {
            $this->cloud->sendText($conversation->contact->phone, $content, $conversation);
        }

        return ['mode' => $conversation->attention_mode, 'conversation' => $conversation, 'message' => $message, 'response' => $outbound];
    }

    private function canAutoRespond(array $result, ?DerivationArea $area, string $action): bool
    {
        $intention = $result['intention'] ?? null;
        $confidence = (float) ($result['confidence'] ?? 0);
        $sources = $result['sources'] ?? [];

        if ($area || $action !== Intention::ACTION_RESPOND_AI || empty($sources)) {
            return false;
        }

        if ($intention && in_array($intention->slug, ['inversion', 'reclamo', 'garantia'], true)) {
            return false;
        }

        return ! $intention || $confidence >= (float) $intention->minimum_confidence;
    }

    private function forcedArea(string $body, ?Intention $intention): ?DerivationArea
    {
        $text = Str::lower(Str::ascii($body));
        $slug = $intention?->slug;

        return match (true) {
            $slug === 'inversion' => DerivationArea::where('name', 'Gerencia Comercial')->first(),
            in_array($slug, ['reclamo', 'garantia'], true) => DerivationArea::where('name', 'Soporte')->first(),
            $this->containsAny($text, ['comprar', 'pagar', 'pago', 'precio', 'reserva']) => DerivationArea::where('name', 'Ventas')->first(),
            $this->containsAny($text, ['trabajar', 'afiliarme', 'afiliacion', 'registrarme', 'vender']) => DerivationArea::where('name', 'Supervisor Comercial')->first(),
            default => null,
        };
    }

    private function responseText(array $result, ?DerivationArea $area): string
    {
        if ($area) {
            return 'Gracias por escribir a VIANKA GOLD MINING. Derivaremos tu consulta al area '.$area->name.' para atencion especializada.';
        }

        return (string) ($result['content'] ?? 'Gracias por escribir a VIANKA GOLD MINING. Estamos revisando tu consulta.');
    }

    private function extractBody(array $incoming, string $type): string
    {
        return match ($type) {
            'text' => (string) data_get($incoming, 'text.body', ''),
            'image' => (string) data_get($incoming, 'image.caption', ''),
            'video' => (string) data_get($incoming, 'video.caption', ''),
            'document' => (string) data_get($incoming, 'document.caption', data_get($incoming, 'document.filename', '')),
            'location' => 'Ubicacion: '.data_get($incoming, 'location.latitude').','.data_get($incoming, 'location.longitude'),
            default => '',
        };
    }

    private function isMenuRequest(string $body): bool
    {
        return in_array(Str::lower(Str::ascii(trim($body))), ['hola', 'menu', 'menu', 'inicio'], true);
    }

    private function initialMenu(): string
    {
        return "Hola, soy el asistente de VIANKA GOLD MINING. Elige una opcion:\n\n1. Quiero comprar una joya\n2. Quiero trabajar con VIANKA GOLD MINING\n3. Quiero invertir\n4. Ya compre y tengo una consulta";
    }

    private function compactAiResult(array $result): array
    {
        return [
            'provider' => $result['provider'] ?? null,
            'model' => $result['model'] ?? null,
            'content' => $result['content'] ?? null,
            'confidence' => $result['confidence'] ?? null,
            'recommended_action' => $result['recommended_action'] ?? null,
            'sources' => $result['sources'] ?? [],
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
}
