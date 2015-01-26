<?php

class Options {
    const CACHE_FOLDER = './cache/';
    const REMOTE_FOLDER = './cache/remote/';

    private $opts;

    public function __construct($opts=array()) {
        $sanitized= $this->sanitize($opts);

        $defaults = array(
            'crop' => false,
            'scale' => 'false',
            'thumbnail' => false,
            'maxOnly' => false,
            'canvas-color' => 'transparent',
            'output-filename' => false,
            'cacheFolder' => self::CACHE_FOLDER,
            'remoteFolder' => self::REMOTE_FOLDER,
            'quality' => 90,
            'cache_http_minutes' => 20);

        $this->opts = array_merge($defaults, $sanitized);

    }

    public function asHash() {
        return $this->opts;
    }

    private function sanitize($opts) {
        $result = array();

        if($opts != null) {
            $result = $opts;
        }

        return $result;
    }

}