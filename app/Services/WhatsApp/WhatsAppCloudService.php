<?php

namespace App\Services\WhatsApp;

use App\Models\WhatsAppConversation;
use App\Models\WhatsAppOutboundLog;
use App\Models\WhatsAppSetting;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class WhatsAppCloudService
{
    public function __construct(private ?WhatsAppSetting $setting = null)
    {
        $this->setting ??= WhatsAppSetting::active();
    }

    public function sendText(string $to, string $message, ?WhatsAppConversation $conversation = null): array
    {
        return $this->sendMessage($to, [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'text',
            'text' => ['preview_url' => false, 'body' => $message],
        ], 'text', $message, $conversation);
    }

    public function sendImage(string $to, string $mediaIdOrLink, ?string $caption = null, ?WhatsAppConversation $conversation = null): array
    {
        return $this->sendMedia($to, 'image', $mediaIdOrLink, $caption, $conversation);
    }

    public function sendDocument(string $to, string $mediaIdOrLink, ?string $caption = null, ?WhatsAppConversation $conversation = null): array
    {
        return $this->sendMedia($to, 'document', $mediaIdOrLink, $caption, $conversation);
    }

    public function sendAudio(string $to, string $mediaIdOrLink, ?WhatsAppConversation $conversation = null): array
    {
        return $this->sendMedia($to, 'audio', $mediaIdOrLink, null, $conversation);
    }

    public function sendVideo(string $to, string $mediaIdOrLink, ?string $caption = null, ?WhatsAppConversation $conversation = null): array
    {
        return $this->sendMedia($to, 'video', $mediaIdOrLink, $caption, $conversation);
    }

    public function sendLocation(string $to, float $latitude, float $longitude, ?string $name = null, ?string $address = null, ?WhatsAppConversation $conversation = null): array
    {
        return $this->sendMessage($to, [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'location',
            'location' => compact('latitude', 'longitude', 'name', 'address'),
        ], 'location', $name ?: "{$latitude},{$longitude}", $conversation);
    }

    public function downloadMedia(string $mediaId): ?string
    {
        $metadata = $this->request()->get($this->graphUrl($mediaId))->throw()->json();
        $url = $metadata['url'] ?? null;

        if (! $url) {
            return null;
        }

        $binary = $this->request()->get($url)->throw()->body();
        $path = 'whatsapp/media/'.$mediaId;
        Storage::disk('local')->put($path, $binary);

        return $path;
    }

    public function uploadMedia(string $path, string $mimeType): array
    {
        $response = $this->request()
            ->attach('file', fopen($path, 'r'), basename($path))
            ->post($this->graphUrl($this->setting()->phone_number_id.'/media'), [
                'messaging_product' => 'whatsapp',
                'type' => $mimeType,
            ])
            ->throw();

        return $response->json();
    }

    public function markAsRead(string $messageId): array
    {
        $payload = [
            'messaging_product' => 'whatsapp',
            'status' => 'read',
            'message_id' => $messageId,
        ];

        return $this->request()->post($this->graphUrl($this->setting()->phone_number_id.'/messages'), $payload)->throw()->json();
    }

    private function sendMedia(string $to, string $type, string $mediaIdOrLink, ?string $caption, ?WhatsAppConversation $conversation): array
    {
        $media = str_starts_with($mediaIdOrLink, 'http') ? ['link' => $mediaIdOrLink] : ['id' => $mediaIdOrLink];
        if ($caption && in_array($type, ['image', 'document', 'video'], true)) {
            $media['caption'] = $caption;
        }

        return $this->sendMessage($to, [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => $type,
            $type => $media,
        ], $type, $caption, $conversation);
    }

    private function sendMessage(string $to, array $payload, string $type, ?string $body, ?WhatsAppConversation $conversation): array
    {
        try {
            $response = $this->request()->post($this->graphUrl($this->setting()->phone_number_id.'/messages'), $payload);
            $json = $response->json() ?? [];
            $success = $response->successful();

            $this->log($to, $type, $body, $payload, $json, $success, $success ? null : ($json['error']['message'] ?? $response->body()), $conversation);

            return ['success' => $success, 'response' => $json, 'status' => $response->status()];
        } catch (\Throwable $exception) {
            report($exception);
            $this->log($to, $type, $body, $payload, [], false, $exception->getMessage(), $conversation);

            return ['success' => false, 'response' => [], 'error' => $exception->getMessage()];
        }
    }

    private function request(): \Illuminate\Http\Client\PendingRequest
    {
        $setting = $this->setting();
        $token = $setting->accessToken();

        if (! $token) {
            throw new RuntimeException('Token de WhatsApp no configurado.');
        }

        return Http::withToken($token)->acceptJson()->timeout(30);
    }

    private function graphUrl(string $path): string
    {
        return 'https://graph.facebook.com/'.$this->setting()->api_version.'/'.$path;
    }

    private function setting(): WhatsAppSetting
    {
        if (! $this->setting) {
            throw new RuntimeException('Configuracion de WhatsApp no encontrada.');
        }

        return $this->setting;
    }

    private function log(string $to, string $type, ?string $body, array $request, array $response, bool $success, ?string $error, ?WhatsAppConversation $conversation): void
    {
        WhatsAppOutboundLog::create([
            'whatsapp_conversation_id' => $conversation?->id,
            'to_phone' => $to,
            'type' => $type,
            'body' => $body,
            'success' => $success,
            'error_message' => $error,
            'request_json' => $request,
            'response_json' => $response,
        ]);
    }
}
