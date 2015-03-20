<?php

class Image {

    private $inputPath;
    private $localFilePath;
    private $configuration;
    private $valid_http_protocols = array('http', 'https');
    private $fileSystem;

    public function __construct($url='', $configuration=null) {
        $this->inputPath = $this->sanitize($url);
        $this->setConfiguration($configuration);
        $this->fileSystem = new FileSystem();
    }

    public function injectFileSystem(FileSystem $fileSystem) {
        $this->fileSystem = $fileSystem;
    }

    public function getInputPath() {
        return $this->inputPath;
    }

    public function getLocalFilePath() {
        if(!isset($this->localFilePath)) {
            $this->localFilePath = $this->obtainFilePath();
        }
        return $this->localFilePath;
    }

    public function isRemote() {
        return in_array($this->obtainScheme(), $this->valid_http_protocols);
    }

    public function isPanoramic() {
        $imagePath = $this->getLocalFilePath();
        list($width,$height) = $this->fileSystem->getimagesize($imagePath);
        return $width > $height;
    }

    public function obtainFileName() {
        $finfo = pathinfo($this->inputPath);
        list($filename) = explode('?',$finfo['basename']);
        return $filename;
    }

    public function obtainLocalRemotePath() {
        return  $this->configuration->obtainRemoteRootPath() . $this->obtainFileName();
    }

    public function obtainFilePath() {
        $imagePath = $this->getInputPath();
        if($this->isRemote()):
            $imagePath = $this->obtainRemoteFile();
        endif;
        return $this->getActualCheckedFilePath($imagePath);
    }

    private function getActualCheckedFilePath($actualFilePath) {
        if (!$this->fileSystem->file_exists($actualFilePath)):
            $actualFilePath = $_SERVER['DOCUMENT_ROOT'] . $actualFilePath;
            if (!$this->fileSystem->file_exists($actualFilePath)):
                throw new ImageNotFoundException('path: ' . $actualFilePath);
            endif;
            return $actualFilePath;
        endif;
        return $actualFilePath;
    }

    private function obtainRemoteFile() {
        $localRemotePath = $this->obtainLocalRemotePath();
        if (!$this->isCached()):
            $this->downloadTo($localRemotePath);
        endif;
        return $localRemotePath;
    }

    public function isCached() {
        $minutesInCache = $this->configuration->obtainCacheMinutes();
        $filePath = $this->obtainLocalRemotePath();
        $fileExists = $this->fileSystem->file_exists($filePath);
        if($fileExists) {
            return $this->fileNotExpired($filePath, $minutesInCache);
        }
        return $fileExists;
    }

    private function fileNotExpired($filePath, $minutesInCache) {
        return $this->fileSystem->filemtime($filePath) < strtotime('+'. $minutesInCache. ' minutes');
    }

    private function downloadTo($localRemotePath) {
        $imageContent = $this->fileSystem->file_get_contents($this->getInputPath());
        $this->fileSystem->file_put_contents($localRemotePath,$imageContent);
    }

    private function sanitize($path) {
        return urldecode($path);
    }

    private function obtainScheme() {
        if ($this->inputPath == '') return '';
        $purl = parse_url($this->inputPath);
        if(isset($purl['scheme'])) return $purl['scheme'];
        return '';
    }

    private function setConfiguration($configuration) {
        if ($configuration == null) $configuration = new Configuration(array('output-filename'=>'out'));
        if (!($configuration instanceof Configuration)) throw new InvalidArgumentException();
        $this->configuration = $configuration;
    }

}

class ImageNotFoundException extends RuntimeException {
    public function __construct($message = null, $code = null, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}