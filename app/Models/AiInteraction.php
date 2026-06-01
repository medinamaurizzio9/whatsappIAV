<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiInteraction extends Model
{
    protected $fillable = [
        'user_id',
        'provider',
        'model',
        'question',
        'response',
        'intention_id',
        'confidence',
        'action',
        'derivation_area_id',
        'sources_json',
        'input_tokens',
        'output_tokens',
        'total_tokens',
        'cost_estimated',
        'response_time_ms',
        'success',
        'error_message',
        'raw_response_json',
    ];

    protected function casts(): array
    {
        return [
            'sources_json' => 'array',
            'raw_response_json' => 'array',
            'confidence' => 'decimal:2',
            'cost_estimated' => 'decimal:6',
            'success' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function intention(): BelongsTo
    {
        return $this->belongsTo(Intention::class);
    }

    public function derivationArea(): BelongsTo
    {
        return $this->belongsTo(DerivationArea::class);
    }
}
