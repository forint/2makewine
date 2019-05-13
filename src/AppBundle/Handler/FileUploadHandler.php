<?php

namespace AppBundle\Handler;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploadHandler
{
    private static $targetDir;

    public function __construct($targetDir) {
        self::$targetDir = $targetDir;
    }

    public function upload(UploadedFile $file) {

        $fileName = $file->getFilename(); //.'.'.$file->guessExtension()

        if (!file_exists( self::$targetDir.'/'.$file->getFilename() )){
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move(self::$targetDir, $fileName);
        }

        return $fileName;
    }

    static public function getTargetDir() {
        return self::$targetDir;
    }

}