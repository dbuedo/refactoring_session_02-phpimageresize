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


    public function testImageAlreadyResized() {
        $settings = array('w'=>100,'h'=>100,'crop'=>true);
        $originalPath = './cache/remote/2934973285_fa4761c982.jpg';
        $newPath = './cache/efe167de8d896c225107b6ff9b0c93af_w100_h100_cp_sc.jpg';

        $older_time = 5;
        $newer_time = 10;

        $filemtimeResponseMap = array(
            array($originalPath, $older_time),
            array($newPath, $newer_time)
        );

        $stub = $this->getMockBuilder('FileSystem')
            ->getMock();
        $stub->method('file_exists')
            ->willReturn(true);
        $stub->method('filemtime')
            ->will($this->returnValueMap($filemtimeResponseMap));

        $configuration = new Configuration($settings);
        $image = new Image($originalPath, $configuration);
        $resizer = new Resizer($image, $configuration);

        $resizer->injectFileSystem($stub);

        $this->assertTrue($resizer->alreadyResized($newPath));

    }

    public function testImageNotResizedYet() {
        $settings = array('w'=>100,'h'=>100,'crop'=>true);
        $originalPath = './cache/remote/2934973285_fa4761c982.jpg';
        $newPath = './cache/efe167de8d896c225107b6ff9b0c93af_w100_h100_cp_sc.jpg';


        $originalpathStub = $this->getMockBuilder('FileSystem')
            ->getMock();
        $originalpathStub->method('file_exists')
            ->willReturn(true);

        $newpathStub = $this->getMockBuilder('FileSystem')
            ->getMock();
        $newpathStub->method('file_exists')
            ->willReturn(false);


        $configuration = new Configuration($settings);
        $image = new Image($originalPath, $configuration);
        $resizer = new Resizer($image, $configuration);

        $image->injectFileSystem($originalpathStub);
        $resizer->injectFileSystem($newpathStub);

        $this->assertFalse($resizer->alreadyResized($newPath));

    }

    public function testImageResizedButCacheImageIsOlder() {
        $settings = array('w'=>100,'h'=>100,'crop'=>true);
        $originalPath = './cache/remote/2934973285_fa4761c982.jpg';
        $newPath = './cache/efe167de8d896c225107b6ff9b0c93af_w100_h100_cp_sc.jpg';

        $older_time = 5;
        $newer_time = 10;

        $filemtimeResponseMap = array(
            array($originalPath, $newer_time),
            array($newPath, $older_time)
        );

        $stub = $this->getMockBuilder('FileSystem')
            ->getMock();
        $stub->method('file_exists')
            ->willReturn(true);
        $stub->method('filemtime')
            ->will($this->returnValueMap($filemtimeResponseMap));

        $configuration = new Configuration($settings);
        $image = new Image($originalPath, $configuration);
        $resizer = new Resizer($image, $configuration);

        $resizer->injectFileSystem($stub);

        $this->assertFalse($resizer->alreadyResized($newPath));

    }

    public function testComposeCommand() {
        $settings = array('w'=>10,'h'=>20,'crop'=>true);
        $originalPath = './cache/remote/2934973285_fa4761c982.jpg';
        $newPath = './cache/efe167de8d896c225107b6ff9b0c93af_w100_h100_cp_sc.jpg';

        $configuration = new Configuration($settings);
        $image = new Image($originalPath, $configuration);
        $resizer = new Resizer($image, $configuration);

        $command = $resizer->composeCommand($newPath);

        $this->assertEquals("convert './cache/remote/2934973285_fa4761c982.jpg' -resize '10' -size '10x20' xc:'transparent' +swap -gravity center -composite -quality '90' './cache/efe167de8d896c225107b6ff9b0c93af_w100_h100_cp_sc.jpg'", $command);
    }


    public function testExecuteCommand() {
        $settings = array('w'=>100,'h'=>100,'crop'=>true);
        $originalPath = './cache/remote/2934973285_fa4761c982.jpg';
        $newPath = './cache/efe167de8d896c225107b6ff9b0c93af_w100_h100_cp_sc.jpg';

        $configuration = new Configuration($settings);
        $image = new Image($originalPath, $configuration);
        $resizer = new Resizer($image, $configuration);

        $command = $resizer->composeCommand($newPath);

        $result = $resizer->executeResizeCommand($command);

        $this->assertTrue($result);
    }

    public function testResize() {
        $settings = array('w'=>100,'h'=>100,'crop'=>true);
        $originalPath = './cache/remote/2934973285_fa4761c982.jpg';
        $newPath = './cache/efe167de8d896c225107b6ff9b0c93af_w100_h100_cp_sc.jpg';
        $expectedResultImage = str_replace($_SERVER['DOCUMENT_ROOT'], '', $newPath);

        $configuration = new Configuration($settings);
        $image = new Image($originalPath, $configuration);
        $resizer = new Resizer($image, $configuration);

        $resultImage = $resizer->resize();

        $this->assertEquals($expectedResultImage, $resultImage);
    }


}
