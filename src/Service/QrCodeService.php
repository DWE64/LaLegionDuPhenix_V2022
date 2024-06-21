<?php

namespace App\Service;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

class QrCodeService
{
    public function generateQrCode(string $data, int $userId): string
    {
        $filePath = sys_get_temp_dir() . '/qrcode_' . $userId . '.png';

        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($data)
            ->build();

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $result->saveToFile($filePath);

        return $filePath;
    }
}
