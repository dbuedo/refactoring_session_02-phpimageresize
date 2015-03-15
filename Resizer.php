<?php

require 'FileSystem.php';

class Resizer {

    private $path;
    private $configuration;
    private $fileSystem;

    public function __construct($path, $configuration=null) {
        if ($configuration == null) $configuration = new Configuration();
        $this->checkPath($path);
        $this->checkConfiguration($configuration);
        $this->path = $path;
        $this->configuration = $configuration;
        $this->fileSystem = new FileSystem();
    }

    public function injectFileSystem(FileSystem $fileSystem) {
        $this->fileSystem = $fileSystem;
    }

    public function obtainFilePath() {
        $imagePath = $this->path->sanitizedPath();

        if($this->path->isHttpProtocol()):
            $local_filepath = $this->path->obtainLocalFilePath();

            $inCache = $this->path->isCached($this->configuration->obtainCacheMinutes());

            if(!$inCache):
                $this->download($local_filepath);
            endif;
            $imagePath = $local_filepath;
        endif;

        if(!$this->fileSystem->file_exists($imagePath)):
            $imagePath = $_SERVER['DOCUMENT_ROOT'].$imagePath;
            if(!$this->fileSystem->file_exists($imagePath)):
                throw new RuntimeException();
            endif;
        endif;

        return $imagePath;
    }

    public function composeNewPath() {
        $width = $this->configuration->obtainWidth();
        $height = $this->configuration->obtainHeight();
        $imagePath = $this->obtainFilePath();

        $filename = md5_file($imagePath);
        $finfo = pathinfo($imagePath);
        $ext = $finfo['extension'];

        $opts = $this->configuration->asHash();

        $cropSignal = isset($opts['crop']) && $opts['crop'] == true ? "_cp" : "";
        $scaleSignal = isset($opts['scale']) && $opts['scale'] == true ? "_sc" : "";
        $widthSignal = !empty($width) ? '_w'.$width : '';
        $heightSignal = !empty($height) ? '_h'.$height : '';
        $extension = '.'.$ext;

        $newPath = $this->configuration->obtainCache() .$filename.$widthSignal.$heightSignal.$cropSignal.$scaleSignal.$extension;

        if($opts['output-filename']) {
            $newPath = $opts['output-filename'];
        }

        return $newPath;
    }

    private function download($filePath) {
        $img = $this->fileSystem->file_get_contents($this->path->sanitizedPath());
        $this->fileSystem->file_put_contents($filePath,$img);
    }

    private function checkPath($path) {
        if (!($path instanceof Image)) throw new InvalidArgumentException();
    }

    private function checkConfiguration($configuration) {
        if (!($configuration instanceof Configuration)) throw new InvalidArgumentException();
    }

}