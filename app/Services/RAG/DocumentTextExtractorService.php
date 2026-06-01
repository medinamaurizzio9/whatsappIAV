<?php

namespace App\Services\RAG;

use Illuminate\Support\Facades\Storage;
use ZipArchive;

class DocumentTextExtractorService
{
    public function extract(?string $path, ?string $type = null): string
    {
        if (! $path || ! Storage::disk('public')->exists($path)) {
            return '';
        }

        $type = strtolower((string) $type);
        $absolute = Storage::disk('public')->path($path);

        return match ($type) {
            'txt' => (string) file_get_contents($absolute),
            'docx' => $this->extractDocx($absolute),
            'png', 'jpg', 'jpeg' => '[OCR pendiente] Imagen cargada para indexacion OCR: '.basename($path),
            'pdf' => '[PDF cargado] '.$this->safeBinarySnippet($absolute),
            default => $this->safeBinarySnippet($absolute),
        };
    }

    private function extractDocx(string $absolute): string
    {
        $zip = new ZipArchive();

        if ($zip->open($absolute) !== true) {
            return '';
        }

        $xml = $zip->getFromName('word/document.xml') ?: '';
        $zip->close();

        return trim(strip_tags(str_replace(['</w:p>', '</w:tr>'], "\n", $xml)));
    }

    private function safeBinarySnippet(string $absolute): string
    {
        $content = (string) file_get_contents($absolute, false, null, 0, 4000);

        return trim(preg_replace('/[^\PC\s]/u', '', $content) ?: '');
    }
}
