<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class KnowledgeFaq extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'knowledge_category_id',
        'question',
        'answer',
        'keywords',
        'priority',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'priority' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(KnowledgeCategory::class, 'knowledge_category_id');
    }

    public function intentions(): BelongsToMany
    {
        return $this->belongsToMany(Intention::class, 'intention_knowledge_faq')->withTimestamps();
    }
}
