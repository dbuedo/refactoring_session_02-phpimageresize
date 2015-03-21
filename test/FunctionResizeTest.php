<?php
require_once 'function.resize.php';

class FunctionResizeTest extends PHPUnit_Framework_TestCase {

    public function testResizeWidthLocalImage() {
        $settings = array('w'=>300);
        $finalImage = resize('images/dog.jpg',$settings);

        $this->assertStringMatchesFormat('./cache/%x_w300_sc.jpg',  $finalImage);
        $this->assertFileExists($finalImage);
    }


    public function testResizeWidthAndHeihgtLocalImage() {
        $settings = array('w'=>300, 'h'=>300);
        $finalImage = resize('images/dog.jpg',$settings);

        $this->assertStringMatchesFormat('./cache/%x_w300_h300_sc.jpg',  $finalImage);
        $this->assertFileExists($finalImage);
    }

    public function testResizeCropLocalImage() {
        $settings = array('w'=>300, 'h'=>300, 'crop'=>1);
        $finalImage = resize('images/dog.jpg',$settings);

        $this->assertStringMatchesFormat('./cache/%x_w300_h300_cp_sc.jpg',  $finalImage);
        $this->assertFileExists($finalImage);
    }

    public function testResizeCropRemoteImage() {
        $settings = array('w'=>100,'h'=>100,'crop'=>true);
        $finalImage = resize('http://farm4.static.flickr.com/3210/2934973285_fa4761c982.jpg',$settings);

        $this->assertStringMatchesFormat('./cache/%x_w100_h100_cp_sc.jpg',  $finalImage);
        $this->assertFileExists($finalImage);
    }

    public function testNoOptions() {
        $settings = array();
        $finalImage = resize('images/dog.jpg',$settings);

        $this->assertEquals('cannot resize the image', $finalImage);
    }

    public function testImageNotFound() {
        $settings = array('w'=>300);
        $finalImage = resize('images/not-found.jpg',$settings);

        $this->assertEquals('image not found', $finalImage);
    }


}

