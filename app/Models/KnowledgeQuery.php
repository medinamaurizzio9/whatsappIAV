<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class KnowledgeQuery extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'intention_id',
        'simulated_confidence',
        'recommended_action',
        'derivation_area_id',
        'question',
        'generated_answer',
        'sources',
        'queried_at',
    ];

    protected function casts(): array
    {
        return [
            'sources' => 'array',
            'queried_at' => 'datetime',
            'simulated_confidence' => 'decimal:2',
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
