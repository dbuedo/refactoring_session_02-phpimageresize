<?php

class PathComposer {

    private $configuration;
    private $image;
    private $composedPath;

    public function __construct($configuration = null, $image = null) {
        $this->checkConfiguration($configuration);
        $this->checkImage($image);
        $this->configuration = $configuration;
        $this->image = $image;
        $this->init();
    }

    public function compose() {
        if($this->composedPath) return $this->composedPath;

        $this->composedPath = $this->addRootPath();
        $this->composedPath .= $this->addEncodedFilename();
        $this->composedPath .= $this->addWidthSignal();
        $this->composedPath .= $this->addHeightSignal();
        $this->composedPath .= $this->addCropSignal();
        $this->composedPath .= $this->addScaleSignal();
        $this->composedPath .= $this->addExtension();

        return $this->composedPath;
    }

    private function init() {
        if($this->configuration->obtainOutputFilename()) {
            $this->composedPath = $this->configuration->obtainOutputFilename();
        }
    }

    private function addRootPath() {
       return $this->configuration->obtainCacheRootPath();
    }

    private function addEncodedFilename() {
        $imagePath = $this->image->getLocalFilePath();
        return md5_file($imagePath);
    }

    private function addWidthSignal() {
        $width = $this->configuration->obtainWidth();
        return !empty($width) ? '_w'.$width : '';
    }

    private function addHeightSignal() {
        $height = $this->configuration->obtainHeight();
        return !empty($height) ? '_h'.$height : '';
    }

    private function addCropSignal() {
        $crop = $this->configuration->obtainCrop();
        return isset($crop) && $crop == true ? '_cp' : '';
    }

    private function addScaleSignal() {
        $scale = $this->configuration->obtainScale();
        return isset($scale) && $scale == true ? '_sc' : '';
    }

    private function addExtension() {
        $imagePath = $this->image->getLocalFilePath();
        $fileInfo = pathinfo($imagePath);
        return '.' . $fileInfo['extension'];
    }

    private function checkConfiguration($configuration) {
        if (!($configuration instanceof Configuration)) throw new InvalidArgumentException();
    }

    private function checkImage($image) {
        if (!($image instanceof Image)) throw new InvalidArgumentException();
    }

}