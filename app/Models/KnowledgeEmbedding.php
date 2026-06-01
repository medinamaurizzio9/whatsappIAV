<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeEmbedding extends Model
{
    protected $fillable = [
        'source_type',
        'source_id',
        'chunk_index',
        'content',
        'embedding_model',
        'embedding_vector',
        'tokens',
    ];

    protected function casts(): array
    {
        return [
            'embedding_vector' => 'array',
            'tokens' => 'integer',
        ];
    }
}
