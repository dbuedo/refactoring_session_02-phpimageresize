<?php

require_once 'Resizer.php';
require_once 'Image.php';
require_once 'Configuration.php';
date_default_timezone_set('Europe/Berlin');


class ResizerTest extends PHPUnit_Framework_TestCase {

    /**
     * @expectedException InvalidArgumentException
     */
    public function testNecessaryCollaboration() {
        $resizer = new Resizer('anyNonPathObject');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testOptionalCollaboration() {
        $resizer = new Resizer(new Image(''), 'nonConfigurationObject');
    }

    public function testInstantiation() {
        $this->assertInstanceOf('Resizer', new Resizer(new Image(''), new Configuration(array('w' => 10))));
    }

    public function testComposeNewPath() {
        $configuration = new Configuration(array('w' => 100, 'h' => 100));
        $imagePath = new Image('images/dog.jpg', $configuration);
        $resizer = new Resizer($imagePath, $configuration);

        $newPath = $resizer->composeNewPath();

        $this->assertStringMatchesFormat('./cache/%x_w100_h100_sc.jpg',  $newPath);
    }

    public function testComposeNewPathWithOutputFileName() {
        $configuration = new Configuration(array('output-filename' => 'out.jpg'));
        $imagePath = new Image('images/dog.jpg', $configuration);
        $resizer = new Resizer($imagePath, $configuration);

        $newPath = $resizer->composeNewPath();

        $this->assertEquals('out.jpg',  $newPath);
    }

    public function testResizeCropRemoteImage() {
        $settings = array('w'=>100,'h'=>100,'crop'=>true);
        $imagePath = 'http://farm4.static.flickr.com/3210/2934973285_fa4761c982.jpg';

        $configuration = new Configuration($settings);
        $image = new Image($imagePath, $configuration);
        $resizer = new Resizer($image, $configuration);

        $downloadedImagePath = $image->obtainFilePath();
        $finalImagePath = $resizer->composeNewPath();

        $this->assertEquals('./cache/remote/2934973285_fa4761c982.jpg',  $downloadedImagePath);
        $this->assertStringMatchesFormat('./cache/%x_w100_h100_cp_sc.jpg',  $finalImagePath);
    }

}
