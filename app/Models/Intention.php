<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Intention extends Model
{
    use SoftDeletes;

    public const ACTION_RESPOND_AI = 'responder_ia';
    public const ACTION_DERIVE = 'derivar';
    public const ACTION_RESPOND_AND_DERIVE = 'responder_y_derivar';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'icon',
        'priority',
        'is_active',
        'default_action',
        'derivation_area_id',
        'minimum_confidence',
        'specific_prompt',
        'keywords',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'priority' => 'integer',
            'minimum_confidence' => 'decimal:2',
        ];
    }

    public function derivationArea(): BelongsTo
    {
        return $this->belongsTo(DerivationArea::class);
    }

    public function faqs(): BelongsToMany
    {
        return $this->belongsToMany(KnowledgeFaq::class, 'intention_knowledge_faq')->withTimestamps();
    }

    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(KnowledgeDocument::class, 'intention_knowledge_document')->withTimestamps();
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'intention_product')->withTimestamps();
    }

    public function promotions(): BelongsToMany
    {
        return $this->belongsToMany(Promotion::class, 'intention_promotion')->withTimestamps();
    }

    public function raffles(): BelongsToMany
    {
        return $this->belongsToMany(Raffle::class, 'intention_raffle')->withTimestamps();
    }
}
