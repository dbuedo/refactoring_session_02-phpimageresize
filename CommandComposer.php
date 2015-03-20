<?php


class CommandComposer {

    private $configuration;

    public function __construct($configuration=null) {
        $this->checkConfiguration($configuration);
        $this->configuration = $configuration;
    }

    public function defaultCommand($imagePath, $outputPath) {
        $command = $this->configuration->obtainConvertPath();
        $command .= $this->addEscapedParam($imagePath);
        $command .= $this->addThumbnailParam();
        $command .= $this->addMaxOnlyParam();
        $command .= $this->addQualityParam();
        $command .= $this->addEscapedParam($outputPath);

        return $command;
    }

    public function withCropCommand($imagePath, $outputPath, $isPanoramic) {
        $command = $this->configuration->obtainConvertPath();
        $command .= $this->addEscapedParam($imagePath);
        $command .= $this->addResizeParam($isPanoramic);
        $command .= $this->addSizeParam();
        $command .= $this->addCanvasColorParam();
        $command .= $this->addSwapParam();
        $command .= $this->addGravityParam();
        $command .= $this->addCompositeParam();
        $command .= $this->addQualityParam();
        $command .= $this->addEscapedParam($outputPath);

        return $command;
    }


    private function addEscapedParam($imagePath) {
        return " " . escapeshellarg($imagePath);
    }

    private function addThumbnailParam() {
        $w = $this->configuration->obtainWidth();
        $h =  $this->configuration->obtainHeight();
        return " -thumbnail " . (!empty($h) ? 'x' : '') . $w . "";
    }

    private function addMaxOnlyParam() {
        $maxOnly = $this->configuration->obtainMaxOnly();
        return (isset($maxOnly) && $maxOnly == true ? "\>" : "");
    }

    private function addQualityParam() {
        $quality = $this->configuration->obtainQuality();
        return " -quality " . escapeshellarg($quality);
    }

    private function addCanvasColorParam() {
        $canvasColor = $this->configuration->obtainCanvasColor();
        return " xc:" . escapeshellarg($canvasColor);
    }

    private function addSizeParam() {
        $w = $this->configuration->obtainWidth();
        $h =  $this->configuration->obtainHeight();
        return " -size " . escapeshellarg($w . "x" . $h);
    }

    private function addSwapParam() {
        return " +swap";
    }

    private function addGravityParam() {
        return " -gravity center";
    }

    private function addCompositeParam() {
        return " -composite";
    }

    private function addResizeParam($isPanoramic) {
        $resize = $this->resizeOptions($isPanoramic);
        return " -resize " . escapeshellarg($resize);
    }

    private function resizeOptions($isPanoramic) {
        $hasCrop = $this->configuration->obtainCrop();
        $w = $this->configuration->obtainWidth();
        $h = $this->configuration->obtainHeight();

        $resize = "x".$h;

        if(!$hasCrop && $isPanoramic):
            $resize = $w;
        endif;

        if($hasCrop && !$isPanoramic):
            $resize = $w;
        endif;

        return $resize;
    }

    private function checkConfiguration($configuration) {
        if (!($configuration instanceof Configuration)) throw new InvalidArgumentException();
    }




}