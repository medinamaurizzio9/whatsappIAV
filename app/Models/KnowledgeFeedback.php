<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KnowledgeFeedback extends Model
{
    protected $fillable = [
        'user_id',
        'intention_id',
        'provider',
        'question',
        'generated_answer',
        'correct_answer',
        'rating',
        'comment',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function intention(): BelongsTo
    {
        return $this->belongsTo(Intention::class);
    }
}
