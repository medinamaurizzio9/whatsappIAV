<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SimulatedConversation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'client_id',
        'initial_menu_option_id',
        'channel',
        'client_message',
        'system_response',
        'response_type',
        'derivation_area_id',
        'responded_at',
    ];

    protected function casts(): array
    {
        return [
            'responded_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function initialMenuOption(): BelongsTo
    {
        return $this->belongsTo(InitialMenuOption::class);
    }

    public function derivationArea(): BelongsTo
    {
        return $this->belongsTo(DerivationArea::class);
    }
}
