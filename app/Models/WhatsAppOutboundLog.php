<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppOutboundLog extends Model
{
    protected $table = 'whatsapp_outbound_logs';

    protected $fillable = ['whatsapp_conversation_id', 'to_phone', 'type', 'body', 'success', 'error_message', 'request_json', 'response_json'];

    protected function casts(): array
    {
        return ['success' => 'boolean', 'request_json' => 'array', 'response_json' => 'array'];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(WhatsAppConversation::class, 'whatsapp_conversation_id');
    }
}
