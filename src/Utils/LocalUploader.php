<?php

namespace App\Utils;

use App\Utils\Interfaces\UploaderInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class LocalUploader implements UploaderInterface
{
    private $targetDirectory;
    public $file;

    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }
    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
    public function upload($file)
    {
        $fileName = md5(uniqid()).'.'.$file->guessExtension();
        try{
            $file->move($this->getTargetDirectory(), $fileName);
        }catch(FileException $e){
            //handle exception
            //show errors
            $errors = $e->getMessage();
            dd($errors);
            die();
        }
        $originalName = $this->clear(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        return [$fileName, $originalName];
    }

    private function clear($string)
    {
        return preg_replace('/[^A-Za-z0-9\- ]+/', '', $string);
    }

    public function delete($path)
    {
        $fileSystem = new Filesystem();
        try {
            $fileSystem->remove($path);
        } catch (IOExceptionInterface $exception) {
            return false;
        }
        return true;
    }

}