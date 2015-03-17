<?php

require 'Image.php';
require 'Configuration.php';
require 'Resizer.php';

function sanitize($path) {
	return urldecode($path);
}

function isInCache($newPath, $originalPath) {
	$isInCache = false;
	if(file_exists($newPath) == true):
		$isInCache = true;
		$origFileTime = date("YmdHis",filemtime($originalPath));
		$newFileTime = date("YmdHis",filemtime($newPath));
		if($newFileTime < $origFileTime): # Not using $opts['expire-time'] ??
			$isInCache = false;
		endif;
	endif;

	return $isInCache;
}


function defaultShellCommand($image, $newPath, $configuration) {
	$opts = $configuration->asHash();
	$w = $configuration->obtainWidth();
	$h = $configuration->obtainHeight();
    $imagePath = $image->obtainFilePath();
	$command = $configuration->obtainConvertPath() ." " . escapeshellarg($imagePath) .
	" -thumbnail ". (!empty($h) ? 'x':'') . $w ."".
	(isset($opts['maxOnly']) && $opts['maxOnly'] == true ? "\>" : "") .
	" -quality ". escapeshellarg($opts['quality']) ." ". escapeshellarg($newPath);

	return $command;
}

function composeResizeOptions($image, $configuration) {
	$opts = $configuration->asHash();
	$w = $configuration->obtainWidth();
	$h = $configuration->obtainHeight();
    $imagePath = $image->obtainFilePath();
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

function commandWithScale($image, $newPath, $configuration) {
	$opts = $configuration->asHash();
    $imagePath = $image->obtainFilePath();
	$resize = composeResizeOptions($imagePath, $configuration);

	$cmd = $configuration->obtainConvertPath() ." ". escapeshellarg($imagePath) ." -resize ". escapeshellarg($resize) .
		" -quality ". escapeshellarg($opts['quality']) . " " . escapeshellarg($newPath);

	return $cmd;
}

function commandWithCrop($image, $newPath, $configuration) {
	$opts = $configuration->asHash();
	$w = $configuration->obtainWidth();
	$h = $configuration->obtainHeight();
    $imagePath = $image->obtainFilePath();
	$resize = composeResizeOptions($imagePath, $configuration);

	$cmd = $configuration->obtainConvertPath() ." ". escapeshellarg($imagePath) ." -resize ". escapeshellarg($resize) .
		" -size ". escapeshellarg($w ."x". $h) .
		" xc:". escapeshellarg($opts['canvas-color']) .
		" +swap -gravity center -composite -quality ". escapeshellarg($opts['quality'])." ".escapeshellarg($newPath);

	return $cmd;
}

function doResize($originalImage, $newPath, $configuration) {
	$opts = $configuration->asHash();
	$w = $configuration->obtainWidth();
	$h = $configuration->obtainHeight();

	if(!empty($w) and !empty($h)):
		$cmd = commandWithCrop($originalImage, $newPath, $configuration);
		if(true === $opts['scale']):
			$cmd = commandWithScale($originalImage, $newPath, $configuration);
		endif;
	else:
		$cmd = defaultShellCommand($originalImage, $newPath, $configuration);
	endif;

	$c = exec($cmd, $output, $return_code);
	if($return_code != 0) {
		error_log("Tried to execute : $cmd, return code: $return_code, output: " . print_r($output, true));
		throw new RuntimeException();
	}
}

function resize($originalPath,$opts=null){
    try {
        $configuration = new Configuration($opts);
    } catch(InvalidArgumentException $e) {
        return 'cannot resize the image';
    }
    $originalImage = new Image($originalPath, $configuration);

	$resizer = new Resizer($originalImage, $configuration);


	// This has to be done in resizer resize
	try {
		$originalPath = $originalImage->obtainFilePath();
        $newPath = $resizer->composeNewPath();
	} catch (Exception $e) {
		return 'image not found';
	}

    $create = !isInCache($newPath, $originalPath);
	if($create == true):
		try {
			doResize($originalImage, $newPath, $configuration);
		} catch (Exception $e) {
			return 'cannot resize the image';
		}
	endif;

	// The new path must be the return value of resizer resize
	$cacheFilePath = str_replace($_SERVER['DOCUMENT_ROOT'],'',$newPath);

	return $cacheFilePath;
	
}
