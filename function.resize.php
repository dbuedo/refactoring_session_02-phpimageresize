<?php

require 'Image.php';
require 'Configuration.php';
require 'Resizer.php';


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
    $opts = $configuration->asHash();
    $w = $configuration->obtainWidth();
    $h = $configuration->obtainHeight();

    $commandComposer = new CommandComposer($configuration);

    if (!empty($w) and !empty($h)):
        $cmd = $commandComposer->withCropCommand($originalImage->obtainFilePath(), $newPath, $originalImage->isPanoramic());
        if (true === $opts['scale']):
            $cmd = _commandWithScale($originalImage, $newPath, $configuration);
            return $cmd;
        endif;
        return $cmd;
    else:
        $cmd = $commandComposer->defaultCommand($originalImage->obtainFilePath(), $newPath);
        return $cmd;
    endif;
}

function _composeResizeOptions($image, $configuration) {
	$opts = $configuration->asHash();
	$w = $configuration->obtainWidth();
	$h = $configuration->obtainHeight();
	$resize = "x".$h;

	$hasCrop = (true === $opts['crop']);

    if(!$hasCrop && $image->isPanoramic()):
        $resize = $w;
    endif;

    if($hasCrop && !$image->isPanoramic()):
        $resize = $w;
    endif;


    return $resize;
}

function _commandWithScale($image, $newPath, $configuration) {
	$opts = $configuration->asHash();
    $imagePath = $image->obtainFilePath();
	$resize = _composeResizeOptions($image, $configuration);

	$cmd = $configuration->obtainConvertPath() ." ". escapeshellarg($imagePath) ." -resize ". escapeshellarg($resize) .
		" -quality ". escapeshellarg($opts['quality']) . " " . escapeshellarg($newPath);

	return $cmd;
}





