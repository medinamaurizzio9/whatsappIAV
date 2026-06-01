<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaTranscription extends Model
{
    protected $fillable = [
        'source_type',
        'source_id',
        'file_path',
        'media_type',
        'transcribed_text',
        'duration_seconds',
        'language',
        'status',
    ];
}
