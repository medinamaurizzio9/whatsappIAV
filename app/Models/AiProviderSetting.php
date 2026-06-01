<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class AiProviderSetting extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'provider',
        'model',
        'api_key_encrypted',
        'api_key_last_four',
        'endpoint',
        'temperature',
        'max_tokens',
        'timeout_seconds',
        'is_active',
        'is_default',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'temperature' => 'decimal:2',
            'max_tokens' => 'integer',
            'timeout_seconds' => 'integer',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ];
    }

    public function setApiKey(?string $apiKey): void
    {
        if (! $apiKey) {
            return;
        }

        $this->api_key_encrypted = Crypt::encryptString($apiKey);
        $this->api_key_last_four = substr($apiKey, -4);
    }

    public function apiKey(): ?string
    {
        return $this->api_key_encrypted ? Crypt::decryptString($this->api_key_encrypted) : null;
    }

    public function maskedApiKey(): string
    {
        return $this->api_key_last_four ? '••••••••'.$this->api_key_last_four : 'Sin API key';
    }
}
