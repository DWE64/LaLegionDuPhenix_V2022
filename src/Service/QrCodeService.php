<?php
namespace App\Service;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Builder\BuilderInterface;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class QrCodeService
{
    private BuilderInterface $builder;
    private ParameterBagInterface $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->builder = Builder::create();
        $this->params = $params;
    }

    public function generateQrCode(string $data): string
    {
        $result = $this->builder
            ->writer(new PngWriter())
            ->data($data)
            ->build();

        // Enregistrer l'image du QR code dans un fichier
        $filePath = $this->params->get('kernel.project_dir') . '/var/qrcode.png';
        $result->saveToFile($filePath);

        return $filePath;
    }
}