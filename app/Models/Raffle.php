<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Raffle extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'description', 'prizes', 'raffle_date', 'rules', 'is_active'];

    protected function casts(): array
    {
        return [
            'raffle_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function intentions(): BelongsToMany
    {
        return $this->belongsToMany(Intention::class, 'intention_raffle')->withTimestamps();
    }
}
