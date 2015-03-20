<?php

require 'Image.php';
require 'Configuration.php';
require 'Resizer.php';
require 'CommandComposer.php';


function resize($originalPath,$opts=null){
    try {
        $configuration = new Configuration($opts);
    } catch(InvalidArgumentException $e) {
        return 'cannot resize the image';
    }
    $originalImage = new Image($originalPath, $configuration);

    $resizer = new Resizer($originalImage, $configuration);

    try {
        $resizedImagePath = _doResize($resizer, $originalImage, $configuration);
    } catch (ImageNotFoundException $e) {
        return 'image not found';
    } catch (Exception $e) {
        return 'cannot resize the image';
    }

    return $resizedImagePath;
}


function _doResize($resizer, $originalImage, $configuration) {
    $newPath = $resizer->composeNewPath();

    if (!$resizer->isImageAlreadyResized()):
        $command = _buildCommand($originalImage, $newPath, $configuration);
        _executeCommand($command);
    endif;

    $cacheFilePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $newPath);
    return $cacheFilePath;
}


function _executeCommand($command) {
    exec($command, $output, $return_code);
    if ($return_code != 0) {
        error_log("Tried to execute : $command, return code: $return_code, output: " . print_r($output, true));
        throw new RuntimeException();
    }
}

function _buildCommand($originalImage, $newPath, $configuration) {
    $commandComposer = new CommandComposer($configuration);
    return $commandComposer->composeCommand($originalImage->obtainFilePath(), $newPath, $originalImage->isPanoramic());

}






