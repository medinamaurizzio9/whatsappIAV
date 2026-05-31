<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DerivationArea extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'whatsapp_number',
        'email',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
