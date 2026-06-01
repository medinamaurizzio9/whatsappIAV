<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppMediaFile extends Model
{
    protected $table = 'whatsapp_media_files';

    protected $fillable = ['whatsapp_message_id', 'media_id', 'type', 'mime_type', 'filename', 'sha256', 'storage_path', 'size', 'payload_json'];

    protected function casts(): array
    {
        return ['payload_json' => 'array', 'size' => 'integer'];
    }

    public function message(): BelongsTo
    {
        return $this->belongsTo(WhatsAppMessage::class, 'whatsapp_message_id');
    }
}
