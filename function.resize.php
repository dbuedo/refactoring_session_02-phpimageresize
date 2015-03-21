<?php

require 'Image.php';
require 'Configuration.php';
require 'Resizer.php';
require 'CommandComposer.php';


function resize($originalPath,$opts=null){
    try {

        $configuration = new Configuration($opts);
        $originalImage = new Image($originalPath, $configuration);
        $resizer = new Resizer($originalImage, $configuration);

        return $resizer->resize();

    } catch(InvalidArgumentException $e) {
        return 'cannot resize the image';
    } catch (ImageNotFoundException $e) {
        return 'image not found';
    } catch (Exception $e) {
        return 'cannot resize the image';
    }
}








