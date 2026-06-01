<?php

namespace App\Console\Commands;

use App\Services\WhatsApp\WhatsAppCloudService;
use Illuminate\Console\Command;

class WhatsAppTestSendCommand extends Command
{
    protected $signature = 'whatsapp:test-send {phone} {message}';

    protected $description = 'Envia un mensaje de prueba por WhatsApp Cloud API.';

    public function handle(WhatsAppCloudService $whatsapp): int
    {
        $result = $whatsapp->sendText($this->argument('phone'), $this->argument('message'));

        $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return $result['success'] ? self::SUCCESS : self::FAILURE;
    }
}
