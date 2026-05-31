<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class KnowledgeDocument extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'knowledge_category_id',
        'title',
        'description',
        'file_path',
        'original_filename',
        'file_type',
        'file_size',
        'is_active',
        'uploaded_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'uploaded_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(KnowledgeCategory::class, 'knowledge_category_id');
    }

    public function intentions(): BelongsToMany
    {
        return $this->belongsToMany(Intention::class, 'intention_knowledge_document')->withTimestamps();
    }
}
