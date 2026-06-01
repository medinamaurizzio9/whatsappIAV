<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppWebhookLog extends Model
{
    protected $table = 'whatsapp_webhook_logs';

    protected $fillable = ['method', 'event_type', 'payload_json', 'signature', 'is_valid', 'error_message'];

    protected function casts(): array
    {
        return ['payload_json' => 'array', 'is_valid' => 'boolean'];
    }
}
