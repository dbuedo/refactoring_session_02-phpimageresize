<?php
require_once 'Image.php';
require_once 'Configuration.php';

class ImageTest extends PHPUnit_Framework_TestCase {

    public function testIsSanitizedAtInstantiation() {
        $url = 'https://www.google.com/webhp?sourceid=chrome-instant&ion=1&espv=2&ie=UTF-8#safe=off&q=php%20define%20dictionary';
        $expected = 'https://www.google.com/webhp?sourceid=chrome-instant&ion=1&espv=2&ie=UTF-8#safe=off&q=php define dictionary';

        $image = new Image($url);

        $this->assertEquals($expected, $image->getInputPath());
    }

    public function testIsRemote() {
        $image = new Image('https://example.com');
        $this->assertTrue($image->isRemote());

        $image = new Image('ftp://example.com');
        $this->assertFalse($image->isRemote());

        $image = new Image(null);
        $this->assertFalse($image->isRemote());

        $image = new Image('/absolute/local/path');
        $this->assertFalse($image->isRemote());

        $image = new Image('relative/local/path');
        $this->assertFalse($image->isRemote());
    }

    public function testObtainFileName() {
        $url = 'http://martinfowler.com/mf.jpg?query=hello&s=fowler';

        $image = new Image($url);

        $this->assertEquals('mf.jpg', $image->obtainFileName());
    }

    public function testLocalRemotePath() {
        $url = 'http://martinfowler.com/mf.jpg?query=hello&s=fowler';
        $image = new Image($url);

        $this->assertEquals('./cache/remote/mf.jpg', $image->obtainLocalRemotePath());
    }

    public function testIsCached() {
        $configuration = new Configuration(array('output-filename' => 'out', 'cache_http_minutes' => 20));
        $stub = $this->getMockBuilder('FileSystem')
            ->getMock();
        $stub->method('file_get_contents')
            ->willReturn('foo');
        $stub->method('file_exists')
            ->willReturn(true);
        $stub->method('filemtime')
            ->willReturn(10 * 60);

        $image = new Image('http://martinfowler.com/mf.jpg?query=hello&s=fowler', $configuration);
        $image->injectFileSystem($stub);

        $this->assertTrue($image->isCached());

    }

    public function testIsPanoramic() {
         $stub = $this->getMockBuilder('FileSystem')
            ->getMock();
        $stub->method('file_get_contents')
            ->willReturn('foo');
        $stub->method('file_exists')
            ->willReturn(true);
        $stub->method('filemtime')
            ->willReturn(10 * 60);
        $stub->method('getimagesize')
            ->willReturn(array(800, 200));

        $image = new Image('images/dog.jpg');
        $image->injectFileSystem($stub);

        $this->assertTrue($image->isPanoramic());

    }

    public function testIsNotPanoramic() {
        $stub = $this->getMockBuilder('FileSystem')
            ->getMock();
        $stub->method('file_get_contents')
            ->willReturn('foo');
        $stub->method('file_exists')
            ->willReturn(true);
        $stub->method('filemtime')
            ->willReturn(10 * 60);
        $stub->method('getimagesize')
            ->willReturn(array(200, 800));

        $image = new Image('images/dog.jpg');
        $image->injectFileSystem($stub);

        $this->assertFalse($image->isPanoramic());

    }


    public function testObtainLocallyCachedFilePath() {
        $configuration = new Configuration(array('w' => 800, 'h' => 600));
        $image = new Image('http://martinfowler.com/mf.jpg?query=hello&s=fowler', $configuration);

        $stub = $this->getMockBuilder('FileSystem')
            ->getMock();
        $stub->method('file_get_contents')
            ->willReturn('foo');
        $stub->method('file_exists')
            ->willReturn(true);
        $stub->method('filemtime')
            ->willReturn(10 * 60);

        $image->injectFileSystem($stub);

        $this->assertEquals('./cache/remote/mf.jpg', $image->getLocalFilePath());

    }


    public function testLocallyCachedFilePathFail() {
        $configuration = new Configuration(array('w' => 800, 'h' => 600));
        $image = new Image('http://martinfowler.com/mf.jpg?query=hello&s=fowler', $configuration);

        $stub = $this->getMockBuilder('FileSystem')
            ->getMock();
        $stub->method('file_exists')
            ->willReturn(true);
        $stub->method('filemtime')
            ->willReturn(21 * 60);

        $image->injectFileSystem($stub);

        $this->assertEquals('./cache/remote/mf.jpg', $image->getLocalFilePath());

    }


}
