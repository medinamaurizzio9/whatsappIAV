<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadEvent extends Model
{
    protected $fillable = ['client_id', 'intention_id', 'derivation_area_id', 'evento', 'puntos', 'descripcion'];

    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function intention(): BelongsTo { return $this->belongsTo(Intention::class); }
    public function derivationArea(): BelongsTo { return $this->belongsTo(DerivationArea::class); }
}
