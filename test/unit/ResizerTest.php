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

    public function testObtainLocallyCachedFilePath() {
        $configuration = new Configuration(array('w' => 800, 'h' => 600));
        $imagePath = new Image('http://martinfowler.com/mf.jpg?query=hello&s=fowler', './cache/remote/');
        $resizer = new Resizer($imagePath, $configuration);

        $stub = $this->getMockBuilder('FileSystem')
            ->getMock();
        $stub->method('file_get_contents')
            ->willReturn('foo');
        $stub->method('file_exists')
            ->willReturn(true);
        $stub->method('filemtime')
            ->willReturn(10 * 60);

        $resizer->injectFileSystem($stub);
        $imagePath->injectFileSystem($stub);

        $this->assertEquals('./cache/remote/mf.jpg', $resizer->obtainFilePath());

    }

    public function testLocallyCachedFilePathFail() {
        $configuration = new Configuration(array('w' => 800, 'h' => 600));
        $imagePath = new Image('http://martinfowler.com/mf.jpg?query=hello&s=fowler', './cache/remote/');
        $resizer = new Resizer($imagePath, $configuration);

        $stub = $this->getMockBuilder('FileSystem')
            ->getMock();
        $stub->method('file_exists')
            ->willReturn(true);
        $stub->method('filemtime')
            ->willReturn(21 * 60);

        $resizer->injectFileSystem($stub);
        $imagePath->injectFileSystem($stub);

        $this->assertEquals('./cache/remote/mf.jpg', $resizer->obtainFilePath());

    }

    public function testComposeNewPath() {
        $configuration = new Configuration(array('w' => 100, 'h' => 100));
        $imagePath = new Image('images/dog.jpg');
        $resizer = new Resizer($imagePath, $configuration);

        $newPath = $resizer->composeNewPath();

        $this->assertStringMatchesFormat('./cache/%x_w100_h100_sc.jpg',  $newPath);
    }

    public function testComposeNewPathWithOutputFileName() {
        $configuration = new Configuration(array('output-filename' => 'out.jpg'));
        $imagePath = new Image('images/dog.jpg');
        $resizer = new Resizer($imagePath, $configuration);

        $newPath = $resizer->composeNewPath();

        $this->assertEquals('out.jpg',  $newPath);
    }

}
