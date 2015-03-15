<?php

class Configuration {
    const CACHE_PATH = './cache/';
    const REMOTE_PATH = './cache/remote/';

    const CROP_KEY = 'crop';
    const SCALE_KEY = 'scale';
    const THUMBNAIL_KEY = 'thumbnail';
    const MAX_ONLY_KEY = 'maxOnly';
    const CANVAS_COLOR_KEY = 'canvas-color';
    const OUTPUT_FILE_NAME_KEY = 'output-filename';
    const CACHE_KEY = 'cacheFolder';
    const REMOTE_KEY = 'remoteFolder';
    const QUALITY_KEY = 'quality';
    const CACHE_MINUTES_KEY = 'cache_http_minutes';
    const WIDTH_KEY = 'w';// 'width';
    const HEIGHT_KEY = 'h';//'height';

    const CONVERT_PATH = 'convert';

    private $opts;

    public function __construct($opts=array()) {
        $sanitized= $this->sanitize($opts);

        if(empty($opts[self::OUTPUT_FILE_NAME_KEY])
            && empty($opts[self::WIDTH_KEY])
            && empty($opts[self::HEIGHT_KEY])) {
            throw new InvalidArgumentException();
	    }

        $defaults = array(
            self::CROP_KEY => false,
            self::SCALE_KEY => 'false',
            self::THUMBNAIL_KEY => false,
            self::MAX_ONLY_KEY => false,
            self::CANVAS_COLOR_KEY => 'transparent',
            self::OUTPUT_FILE_NAME_KEY => false,
            self::CACHE_KEY => self::CACHE_PATH,
            self::REMOTE_KEY => self::REMOTE_PATH,
            self::QUALITY_KEY => 90,
            self::CACHE_MINUTES_KEY => 20,
            self::WIDTH_KEY => null,
            self::HEIGHT_KEY => null);

        $this->opts = array_merge($defaults, $sanitized);
    }

    public function asHash() {
        return $this->opts;
    }

    public function obtainCache() {
        return $this->opts[self::CACHE_KEY];
    }

    public function obtainRemote() {
        return $this->opts[self::REMOTE_KEY];
    }

    public function obtainConvertPath() {
        return self::CONVERT_PATH;
    }

    public function obtainWidth() {
        return $this->opts[self::WIDTH_KEY];
    }

    public function obtainHeight() {
        return $this->opts[self::HEIGHT_KEY];
    }

    public function obtainCacheMinutes() {
        return $this->opts[self::CACHE_MINUTES_KEY];
    }
    private function sanitize($opts) {
        if($opts == null) return array();

        return $opts;
    }

}