<?php

use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureUploader
{
    private $newFilename;

    function __construct(UploadedFile $uploadedPicture)
    {
        $destination = __DIR__ . '/../uploads';
        $this->newFilename = uniqid() . '.' . $uploadedPicture->guessExtension();
        $uploadedPicture->move($destination, $this->newFilename);
    }

    function getNewFilename()
    {
        return $this->newFilename;
    }
}