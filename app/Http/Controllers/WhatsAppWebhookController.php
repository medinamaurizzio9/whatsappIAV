<?php

namespace App\Http\Controllers;

use App\Models\WhatsAppSetting;
use App\Models\WhatsAppWebhookLog;
use App\Services\WhatsApp\WhatsAppInboundProcessor;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WhatsAppWebhookController extends Controller
{
    public function verify(Request $request): Response
    {
        $setting = WhatsAppSetting::active();
        $valid = $request->query('hub_mode') === 'subscribe'
            || $request->query('hub.mode') === 'subscribe';
        $token = $request->query('hub_verify_token', $request->query('hub.verify_token'));
        $challenge = $request->query('hub_challenge', $request->query('hub.challenge'));

        WhatsAppWebhookLog::create([
            'method' => 'GET',
            'event_type' => 'verification',
            'payload_json' => $request->query(),
            'is_valid' => (bool) ($setting && $valid && hash_equals($setting->verify_token, (string) $token)),
        ]);

        if ($setting && $valid && hash_equals($setting->verify_token, (string) $token)) {
            return response((string) $challenge, 200);
        }

        return response('Forbidden', 403);
    }

    public function receive(Request $request, WhatsAppInboundProcessor $processor): Response
    {
        $payload = $request->all();
        $signature = (string) $request->header('X-Hub-Signature-256', '');

        if (! $this->validSignature($request, $signature)) {
            WhatsAppWebhookLog::create([
                'method' => 'POST',
                'event_type' => 'message',
                'payload_json' => $payload,
                'signature' => $signature,
                'is_valid' => false,
                'error_message' => 'Firma invalida.',
            ]);

            return response('Invalid signature', 403);
        }

        WhatsAppWebhookLog::create([
            'method' => 'POST',
            'event_type' => 'message',
            'payload_json' => $payload,
            'signature' => $signature,
            'is_valid' => true,
        ]);

        $processor->process($payload);

        return response('EVENT_RECEIVED', 200);
    }

    private function validSignature(Request $request, string $signature): bool
    {
        $secret = WhatsAppSetting::active()?->appSecret();

        if (! $secret) {
            return true;
        }

        $expected = 'sha256='.hash_hmac('sha256', $request->getContent(), $secret);

        return hash_equals($expected, $signature);
    }
}
