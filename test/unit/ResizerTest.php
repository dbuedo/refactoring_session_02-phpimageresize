<?php

require_once 'Resizer.php';
require_once 'ImagePath.php';
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
        $resizer = new Resizer(new ImagePath(''), 'nonConfigurationObject');
    }

    public function testInstantiation() {
        $this->assertInstanceOf('Resizer', new Resizer(new ImagePath(''), new Configuration(array('w' => 10))));
    }

    public function testObtainLocallyCachedFilePath() {
        $configuration = new Configuration(array('w' => 800, 'h' => 600));
        $imagePath = new ImagePath('http://martinfowler.com/mf.jpg?query=hello&s=fowler');
        $resizer = new Resizer($imagePath, $configuration);

        $stub = $this->getMockBuilder('FileSystem')
            ->getMock();
        $stub->method('file_get_contents')
            ->willReturn('foo');

        $stub->method('file_exists')
            ->willReturn(true);

        $resizer->injectFileSystem($stub);

        $this->assertEquals('./cache/remote/mf.jpg', $resizer->obtainFilePath());

    }

    public function testLocallyCachedFilePathFail() {
        $configuration = new Configuration(array('w' => 800, 'h' => 600));
        $imagePath = new ImagePath('http://martinfowler.com/mf.jpg?query=hello&s=fowler');
        $resizer = new Resizer($imagePath, $configuration);

        $stub = $this->getMockBuilder('FileSystem')
            ->getMock();
        $stub->method('file_exists')
            ->willReturn(true);

        $stub->method('filemtime')
            ->willReturn(21 * 60);

        $resizer->injectFileSystem($stub);

        $this->assertEquals('./cache/remote/mf.jpg', $resizer->obtainFilePath());

    }


    public function testComposeNewPath() {
        $configuration = new Configuration(array('w' => 100, 'h' => 100));
        $imagePath = new ImagePath('images/dog.jpg');
        $resizer = new Resizer($imagePath, $configuration);

        $newPath = $resizer->composeNewPath();

        $this->assertStringMatchesFormat('./cache/%x_w100_h100_sc.jpg',  $newPath);
    }

}
