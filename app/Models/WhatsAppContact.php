<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsAppContact extends Model
{
    protected $table = 'whatsapp_contacts';

    protected $fillable = ['wa_id', 'phone', 'name', 'client_id', 'profile_json', 'last_seen_at'];

    protected function casts(): array
    {
        return ['profile_json' => 'array', 'last_seen_at' => 'datetime'];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(WhatsAppConversation::class);
    }
}
