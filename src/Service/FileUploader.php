<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private $gamePictureDirectory;
    private $userPictureDirectory;
    private $qrCodeDirectory;
    private $slugger;

    public function __construct($gamePictureDirectory, $userPictureDirectory, $qrCodeDirectory, SluggerInterface $slugger)
    {
        $this->gamePictureDirectory = $gamePictureDirectory;
        $this->userPictureDirectory = $userPictureDirectory;
        $this->qrCodeDirectory = $qrCodeDirectory;
        $this->slugger = $slugger;
    }

    public function uploadGamePicture(UploadedFile $file)
    {
        return $this->uploadFile($file, $this->getGamePictureDirectory());
    }

    public function uploadUserPicture(UploadedFile $file)
    {
        return $this->uploadFile($file, $this->getUserPictureDirectory());
    }

    public function uploadQrcode(UploadedFile $file)
    {
        return $this->uploadFile($file, $this->getQrCodeDirectory());
    }

    private function uploadFile(UploadedFile $file, $targetDirectory)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move($targetDirectory, $fileName);
        } catch (FileException $e) {
            return $e;
        }

        return $fileName;
    }

    public function getGamePictureDirectory()
    {
        return $this->gamePictureDirectory;
    }

    public function getUserPictureDirectory()
    {
        return $this->userPictureDirectory;
    }

    public function getQrCodeDirectory()
    {
        return $this->qrCodeDirectory;
    }
}
