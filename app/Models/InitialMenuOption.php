<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InitialMenuOption extends Model
{
    use SoftDeletes;

    public const ACTION_IA = 'ia';
    public const ACTION_DERIVATION = 'derivacion';

    protected $fillable = [
        'title',
        'description',
        'is_active',
        'sort_order',
        'action',
        'derivation_area_id',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function derivationArea(): BelongsTo
    {
        return $this->belongsTo(DerivationArea::class);
    }
}
