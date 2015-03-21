<?php

require_once 'Image.php';
require_once 'Configuration.php';
require_once 'PathComposer.php';
require_once 'CommandComposer.php';
require_once 'FileSystem.php';


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

    public function resize() {
        $newPath = $this->composeNewImagePath();

        if (!$this->alreadyResized($newPath)):
            $command = $this->composeCommand($newPath);
            $this->executeResizeCommand($command);
        endif;

        return $this->obtainFullPath($newPath);
    }

    private function composeNewImagePath() {
        $pathComposer = new PathComposer($this->configuration, $this->image);
        return $pathComposer->compose();
    }

    public function alreadyResized($newPath) {
        $isInCache = false;
        if($this->fileSystem->file_exists($newPath)):
            $isInCache = true;
            $origFileTime = $this->obtainLastModificationDate($this->image->getLocalFilePath());
            $newFileTime = $this->obtainLastModificationDate($newPath);
            if($newFileTime < $origFileTime):
                $isInCache = false;
            endif;
        endif;

        return $isInCache;
    }

    public function composeCommand($newPath) {
        $commandComposer = new CommandComposer($this->configuration);
        return $commandComposer->composeCommand($this->image->obtainFilePath(), $newPath, $this->image->isPanoramic());
    }

    public function executeResizeCommand($command) {
        $result = true;
        exec($command, $output, $return_code);
        if ($return_code != 0) {
            error_log("Tried to execute : $command, return code: $return_code, output: " . print_r($output, true));
            throw new RuntimeException();
        }
        return $result;
    }

    public function injectFileSystem(FileSystem $fileSystem) {
        $this->fileSystem = $fileSystem;
    }

    private function obtainLastModificationDate($path) {
        return date("YmdHis",$this->fileSystem->filemtime($path));
    }

    private function obtainFullPath($newPath) {
        return str_replace($_SERVER['DOCUMENT_ROOT'], '', $newPath);
    }

    private function checkPath($path) {
        if (!($path instanceof Image)) throw new InvalidArgumentException();
    }

    private function checkConfiguration($configuration) {
        if (!($configuration instanceof Configuration)) throw new InvalidArgumentException();
    }


}