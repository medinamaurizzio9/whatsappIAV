<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'city',
        'type',
        'observations',
    ];

    public function simulatedConversations(): HasMany
    {
        return $this->hasMany(SimulatedConversation::class);
    }
}
