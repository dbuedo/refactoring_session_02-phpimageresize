<?php

require 'FileSystem.php';

class Resizer {

    private $image;
    private $configuration;
    private $fileSystem;

    public function __construct($image, $configuration=null) {
        if ($configuration == null) $configuration = new Configuration();
        $this->checkPath($image);
        $this->checkConfiguration($configuration);
        $this->image = $image;
        $this->configuration = $configuration;
        $this->fileSystem = new FileSystem();
    }

    public function injectFileSystem(FileSystem $fileSystem) {
        $this->fileSystem = $fileSystem;
    }

    public function composeNewPath() {
        $width = $this->configuration->obtainWidth();
        $height = $this->configuration->obtainHeight();
        $imagePath = $this->image->getLocalFilePath();

        $filename = md5_file($imagePath);
        $finfo = pathinfo($imagePath);
        $ext = $finfo['extension'];

        $opts = $this->configuration->asHash();

        $cropSignal = isset($opts['crop']) && $opts['crop'] == true ? "_cp" : "";
        $scaleSignal = isset($opts['scale']) && $opts['scale'] == true ? "_sc" : "";
        $widthSignal = !empty($width) ? '_w'.$width : '';
        $heightSignal = !empty($height) ? '_h'.$height : '';
        $extension = '.'.$ext;

        $newPath = $this->configuration->obtainCacheRootPath() .$filename.$widthSignal.$heightSignal.$cropSignal.$scaleSignal.$extension;

        if($opts['output-filename']) {
            $newPath = $opts['output-filename'];
        }

        return $newPath;
    }

    public function isImageAlreadyResized() {
        $newPath = $this->composeNewPath();
        $isInCache = false;
        if($this->fileSystem->file_exists($newPath)):
            $isInCache = true;
            $origFileTime = date("YmdHis",$this->fileSystem->filemtime($this->image->getLocalFilePath()));
            $newFileTime = date("YmdHis",$this->fileSystem->filemtime($newPath));
            if($newFileTime < $origFileTime):
                $isInCache = false;
            endif;
        endif;

        return $isInCache;
    }

    private function checkPath($path) {
        if (!($path instanceof Image)) throw new InvalidArgumentException();
    }

    private function checkConfiguration($configuration) {
        if (!($configuration instanceof Configuration)) throw new InvalidArgumentException();
    }

}