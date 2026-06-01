<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsAppConversation extends Model
{
    protected $table = 'whatsapp_conversations';

    protected $fillable = [
        'whatsapp_contact_id',
        'client_id',
        'derivation_area_id',
        'status',
        'attention_mode',
        'last_message_preview',
        'last_message_at',
    ];

    protected function casts(): array
    {
        return ['last_message_at' => 'datetime'];
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(WhatsAppContact::class, 'whatsapp_contact_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function derivationArea(): BelongsTo
    {
        return $this->belongsTo(DerivationArea::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(WhatsAppMessage::class);
    }
}
