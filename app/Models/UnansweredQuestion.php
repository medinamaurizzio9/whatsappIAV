<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnansweredQuestion extends Model
{
    protected $fillable = ['intention_id', 'question', 'reason', 'status'];

    public function intention(): BelongsTo
    {
        return $this->belongsTo(Intention::class);
    }
}
