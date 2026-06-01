<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsAppMessage extends Model
{
    protected $table = 'whatsapp_messages';

    protected $fillable = [
        'whatsapp_conversation_id',
        'direction',
        'message_id',
        'type',
        'body',
        'status',
        'intention_id',
        'confidence',
        'recommended_action',
        'derivation_area_id',
        'requires_approval',
        'payload_json',
        'ai_result_json',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'confidence' => 'decimal:2',
            'requires_approval' => 'boolean',
            'payload_json' => 'array',
            'ai_result_json' => 'array',
            'sent_at' => 'datetime',
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(WhatsAppConversation::class, 'whatsapp_conversation_id');
    }

    public function intention(): BelongsTo
    {
        return $this->belongsTo(Intention::class);
    }

    public function derivationArea(): BelongsTo
    {
        return $this->belongsTo(DerivationArea::class);
    }

    public function mediaFiles(): HasMany
    {
        return $this->hasMany(WhatsAppMediaFile::class);
    }
}
