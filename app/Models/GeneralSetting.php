<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    protected $fillable = [
        'company_name',
        'logo_path',
        'main_phone',
        'address',
        'business_hours',
        'welcome_message',
    ];
}
