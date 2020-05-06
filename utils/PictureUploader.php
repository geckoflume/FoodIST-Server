<?php

use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureUploader
{
    public static $destination = __DIR__ . '/../uploads';
    private $newFilename;

    function __construct(UploadedFile $uploadedPicture)
    {
        $this->newFilename = uniqid() . '.' . $uploadedPicture->guessExtension();
        $uploadedPicture->move(PictureUploader::$destination, $this->newFilename);
    }

    function getNewFilename()
    {
        return $this->newFilename;
    }
}