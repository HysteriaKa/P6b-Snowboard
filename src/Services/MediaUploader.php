<?php

namespace App\Services;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Flex\Event\UpdateEvent;

class MediaUploader
{


    private $video = ["video/mp4", "video/mpeg"];
    private $image = ["image/gif", "image/jpeg", "image/png",  "image/webp"];
    private $imageExtensions = ["gif", "jpeg", "png",  "webp"];
    private $videoExtensions = ["mp4", "mpeg"];
    private $file;
    private $targetDirectory;
    private $slugger;

    public function __construct($targetDirectory, SluggerInterface $slugger, UploadedFile $file)
    {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
        $this->file = $file;
    }


    public function add()
    {
        
        $originalFilename = pathinfo($this->file->getClientOriginalName(), PATHINFO_FILENAME);
      
           
            $type = $this->defineType();
            $safeFilename = $this->slugger->slug($originalFilename);
            $fileName = $safeFilename.'-'.uniqid().'.'.$this->file->guessExtension();
            $this->file->move($this->targetDirectory, $fileName);
          

            return [$fileName, $type];


    }

    private function defineType()
    {
        $filetype = $this->file->getMimeType();
        if (in_array($filetype, $this->video)) return "video";
        if (in_array($filetype, $this->image)) return "image";
        throw "unauhorized";
    }
    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }

}
