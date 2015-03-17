<?php

class Image {

    private $path;
    private $localFolder;
    private $valid_http_protocols = array('http', 'https');
    private $fileSystem;

    public function __construct($url='', $localFolder='') {
        $this->path = $this->sanitize($url);
        $this->localFolder = $localFolder;
        $this->fileSystem = new FileSystem();
    }

    public function injectFileSystem(FileSystem $fileSystem) {
        $this->fileSystem = $fileSystem;
    }

    public function sanitizedPath() {
        return $this->path;
    }

    public function isHttpProtocol() {
        return in_array($this->obtainScheme(), $this->valid_http_protocols);
    }

    public function obtainFileName() {
        $finfo = pathinfo($this->path);
        list($filename) = explode('?',$finfo['basename']);
        return $filename;
    }

    public function obtainLocalFilePath() {
        return $this->localFolder . $this->obtainFileName();
    }

    public function isCached($minutesInCache) {
        $filePath = $this->obtainLocalFilePath();
        $fileExists = $this->fileSystem->file_exists($filePath);
        if($fileExists) {
            return $this->fileNotExpired($filePath, $minutesInCache);
        }
        return $fileExists;
    }

    private function fileNotExpired($filePath, $minutesInCache) {
        return $this->fileSystem->filemtime($filePath) < strtotime('+'. $minutesInCache. ' minutes');
    }


    private function sanitize($path) {
        return urldecode($path);
    }

    private function obtainScheme() {
        if ($this->path == '') return '';
        $purl = parse_url($this->path);
        if(isset($purl['scheme'])) return $purl['scheme'];
        return '';
    }

}