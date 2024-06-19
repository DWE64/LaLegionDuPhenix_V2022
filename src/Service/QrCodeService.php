<?php

namespace App\Service;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

class QrCodeService
{
    public function generateQrCode(string $data): string
    {
        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($data)
            ->build();

        $filePath = sys_get_temp_dir() . '/' . uniqid('qrcode_', true) . '.png';
        $result->saveToFile($filePath);

        return $filePath;
    }
}
