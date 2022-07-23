<?php


namespace App\Service;


use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private $gamePictureDirectory;
    private $userPictureDirectory;
    private $slugger;

    public function __construct($gamePictureDirectory, $userPictureDirectory, SluggerInterface $slugger)
    {
        $this->gamePictureDirectory = $gamePictureDirectory;
        $this->userPictureDirectory = $userPictureDirectory;
        $this->slugger = $slugger;
    }

    public function uploadGamePicture(UploadedFile $file)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->getGamePictureDirectory(), $fileName);
        } catch (FileException $e) {
            return $e;
        }

        return $fileName;
    }

    public function uploadUserPicture(UploadedFile $file)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->getUserPictureDirectory(), $fileName);
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
}