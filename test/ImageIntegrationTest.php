<?php


class ImageIntegrationTest extends PHPUnit_Framework_TestCase {

    public function testIsPanoramicIntegration() {
        $image = new Image('images/dog.jpg');

        $this->assertFalse($image->isPanoramic());
    }

    public function testIsPanoramicRemoteIntegration() {
        $image = new Image('http://farm4.static.flickr.com/3210/2934973285_fa4761c982.jpg');

        $this->assertFalse($image->isPanoramic());
    }
}



