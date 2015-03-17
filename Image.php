<?php

class Image {

    private $path;
    private $configuration;
    private $valid_http_protocols = array('http', 'https');
    private $fileSystem;

    public function __construct($url='', $configuration=null) {
        $this->path = $this->sanitize($url);
        $this->setConfiguration($configuration);
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

    public function isPanoramic() {
        $imagePath = $this->obtainFilePath();
        list($width,$height) = $this->fileSystem->getimagesize($imagePath);
        return $width > $height;
    }

    public function obtainFileName() {
        $finfo = pathinfo($this->path);
        list($filename) = explode('?',$finfo['basename']);
        return $filename;
    }

    public function obtainLocalFilePath() {
        return  $this->configuration->obtainRemote() . $this->obtainFileName();
    }

    public function obtainFilePath() {
        $imagePath = $this->sanitizedPath();

        if($this->isHttpProtocol()):
            $local_filepath = $this->obtainLocalFilePath();

            $inCache = $this->isCached($this->configuration->obtainCacheMinutes());

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

    private function download($filePath) {
        $img = $this->fileSystem->file_get_contents($this->path->sanitizedPath());
        $this->fileSystem->file_put_contents($filePath,$img);
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

    private function setConfiguration($configuration) {
        if ($configuration == null) $configuration = new Configuration(array('output-filename'=>'out'));
        if (!($configuration instanceof Configuration)) throw new InvalidArgumentException();
        $this->configuration = $configuration;
    }

}