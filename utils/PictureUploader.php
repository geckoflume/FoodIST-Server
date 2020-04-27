<?php

use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureUploader
{
    private $baseUrl;
    private $newFilename;

    function __construct(UploadedFile $uploadedPicture)
    {
        $this->baseUrl = 'https://' . $_SERVER['SERVER_NAME'] . '/uploads/';
        $destination = __DIR__ . '/../uploads';
        $this->newFilename = uniqid() . '.' . $uploadedPicture->guessExtension();
        $uploadedPicture->move($destination, $this->newFilename);
    }

    function getNewFilename()
    {
        return $this->baseUrl . $this->newFilename;
    }
}