<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadAlert extends Model
{
    protected $fillable = ['client_id', 'intention_id', 'type', 'title', 'message', 'status', 'read_at'];

    protected function casts(): array
    {
        return ['read_at' => 'datetime'];
    }

    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function intention(): BelongsTo { return $this->belongsTo(Intention::class); }
}
