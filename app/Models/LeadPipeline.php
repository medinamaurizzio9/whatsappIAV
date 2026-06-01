<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadPipeline extends Model
{
    protected $fillable = ['client_id', 'stage', 'assigned_area_id', 'assigned_user_id', 'last_moved_at'];

    protected function casts(): array
    {
        return ['last_moved_at' => 'datetime'];
    }

    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function assignedArea(): BelongsTo { return $this->belongsTo(DerivationArea::class, 'assigned_area_id'); }
    public function assignedUser(): BelongsTo { return $this->belongsTo(User::class, 'assigned_user_id'); }
}
