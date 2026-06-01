<?php

namespace App\Http\Controllers;

use App\Models\DerivationArea;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Models\WhatsAppSetting;
use App\Services\WhatsApp\WhatsAppCloudService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WhatsAppInboxController extends Controller
{
    public function index(): View
    {
        return view('whatsapp.inbox', [
            'conversations' => WhatsAppConversation::with(['contact', 'derivationArea'])->latest('last_message_at')->paginate(15),
        ]);
    }

    public function show(WhatsAppConversation $conversation): View
    {
        return view('whatsapp.conversation', [
            'conversation' => $conversation->load(['contact', 'derivationArea', 'messages.mediaFiles']),
            'areas' => DerivationArea::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function reply(Request $request, WhatsAppConversation $conversation, WhatsAppCloudService $cloud): RedirectResponse
    {
        $data = $request->validate([
            'body' => ['nullable', 'string', 'max:4000', 'required_without:file'],
            'file' => ['nullable', 'file', 'max:20480'],
        ]);

        $type = 'text';
        $body = $data['body'] ?? null;
        $result = [];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $uploaded = $cloud->uploadMedia($file->getRealPath(), (string) $file->getMimeType());
            $mediaId = (string) ($uploaded['id'] ?? '');
            $mime = (string) $file->getMimeType();
            $type = str_starts_with($mime, 'image/') ? 'image' : (str_starts_with($mime, 'audio/') ? 'audio' : (str_starts_with($mime, 'video/') ? 'video' : 'document'));
            $result = match ($type) {
                'image' => $cloud->sendImage($conversation->contact->phone, $mediaId, $body, $conversation),
                'audio' => $cloud->sendAudio($conversation->contact->phone, $mediaId, $conversation),
                'video' => $cloud->sendVideo($conversation->contact->phone, $mediaId, $body, $conversation),
                default => $cloud->sendDocument($conversation->contact->phone, $mediaId, $body, $conversation),
            };
        } else {
            $result = $cloud->sendText($conversation->contact->phone, (string) $body, $conversation);
        }

        WhatsAppMessage::create([
            'whatsapp_conversation_id' => $conversation->id,
            'direction' => 'outbound',
            'type' => $type,
            'body' => $body,
            'status' => $result['success'] ? 'sent' : 'failed',
            'sent_at' => $result['success'] ? now() : null,
            'payload_json' => $result,
        ]);

        return back()->with('status', $result['success'] ? 'Mensaje enviado.' : 'No se pudo enviar el mensaje.');
    }

    public function update(Request $request, WhatsAppConversation $conversation): RedirectResponse
    {
        $data = $request->validate([
            'derivation_area_id' => ['nullable', 'exists:derivation_areas,id'],
            'attention_mode' => ['required', 'in:manual,supervisado,automatico'],
            'status' => ['required', 'in:open,closed'],
        ]);

        $conversation->update($data);

        return back()->with('status', 'Conversacion actualizada.');
    }

    public function approve(WhatsAppMessage $message, WhatsAppCloudService $cloud): RedirectResponse
    {
        abort_unless($message->requires_approval, 404);

        $conversation = $message->conversation()->with('contact')->firstOrFail();
        $result = $cloud->sendText($conversation->contact->phone, (string) $message->body, $conversation);
        $message->update([
            'status' => $result['success'] ? 'sent' : 'failed',
            'requires_approval' => false,
            'sent_at' => $result['success'] ? now() : null,
            'payload_json' => $result,
        ]);

        return back()->with('status', $result['success'] ? 'Respuesta aprobada y enviada.' : 'No se pudo enviar la respuesta.');
    }
}
