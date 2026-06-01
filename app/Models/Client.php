<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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

    public function leadScore(): HasOne { return $this->hasOne(LeadScore::class); }
    public function leadEvents(): HasMany { return $this->hasMany(LeadEvent::class); }
    public function leadPipeline(): HasOne { return $this->hasOne(LeadPipeline::class); }
    public function leadAlerts(): HasMany { return $this->hasMany(LeadAlert::class); }
}
