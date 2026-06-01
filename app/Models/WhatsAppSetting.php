<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class WhatsAppSetting extends Model
{
    protected $table = 'whatsapp_settings';

    public const MODE_MANUAL = 'manual';
    public const MODE_SUPERVISED = 'supervisado';
    public const MODE_AUTOMATIC = 'automatico';

    protected $fillable = [
        'business_account_id',
        'phone_number_id',
        'display_phone_number',
        'access_token_encrypted',
        'access_token_last_four',
        'verify_token',
        'app_secret_encrypted',
        'webhook_url',
        'api_version',
        'attention_mode',
        'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public static function active(): ?self
    {
        return self::where('is_active', true)->latest()->first() ?: self::latest()->first();
    }

    public function setAccessToken(?string $token): void
    {
        if (! $token) {
            return;
        }

        $this->access_token_encrypted = Crypt::encryptString($token);
        $this->access_token_last_four = substr($token, -4);
    }

    public function accessToken(): ?string
    {
        return $this->access_token_encrypted ? Crypt::decryptString($this->access_token_encrypted) : null;
    }

    public function maskedAccessToken(): string
    {
        return $this->access_token_last_four ? '********'.$this->access_token_last_four : 'Sin token';
    }

    public function setAppSecret(?string $secret): void
    {
        if ($secret) {
            $this->app_secret_encrypted = Crypt::encryptString($secret);
        }
    }

    public function appSecret(): ?string
    {
        return $this->app_secret_encrypted ? Crypt::decryptString($this->app_secret_encrypted) : null;
    }
}
