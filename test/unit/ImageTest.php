<?php
require_once 'Image.php';
require_once 'Configuration.php';

class ImageTest extends PHPUnit_Framework_TestCase {

    public function testIsSanitizedAtInstantiation() {
        $url = 'https://www.google.com/webhp?sourceid=chrome-instant&ion=1&espv=2&ie=UTF-8#safe=off&q=php%20define%20dictionary';
        $expected = 'https://www.google.com/webhp?sourceid=chrome-instant&ion=1&espv=2&ie=UTF-8#safe=off&q=php define dictionary';

        $image = new Image($url);

        $this->assertEquals($expected, $image->sanitizedPath());
    }

    public function testIsHttpProtocol() {
        $image = new Image('https://example.com');
        $this->assertTrue($image->isHttpProtocol());

        $image = new Image('ftp://example.com');
        $this->assertFalse($image->isHttpProtocol());

        $image = new Image(null);
        $this->assertFalse($image->isHttpProtocol());

        $image = new Image('/absolute/local/path');
        $this->assertFalse($image->isHttpProtocol());

        $image = new Image('relative/local/path');
        $this->assertFalse($image->isHttpProtocol());
    }

    public function testObtainFileName() {
        $url = 'http://martinfowler.com/mf.jpg?query=hello&s=fowler';

        $image = new Image($url);

        $this->assertEquals('mf.jpg', $image->obtainFileName());
    }

    public function testLocalFilePath() {
        $url = 'http://martinfowler.com/mf.jpg?query=hello&s=fowler';
        $image = new Image($url);

        $this->assertEquals('./cache/remote/mf.jpg', $image->obtainLocalFilePath());
    }

    public function testIsCached() {
        $minutesInCache = 20;
        $stub = $this->getMockBuilder('FileSystem')
            ->getMock();
        $stub->method('file_get_contents')
            ->willReturn('foo');
        $stub->method('file_exists')
            ->willReturn(true);
        $stub->method('filemtime')
            ->willReturn(10 * 60);

        $image = new Image('http://martinfowler.com/mf.jpg?query=hello&s=fowler');
        $image->injectFileSystem($stub);

        $this->assertTrue($image->isCached($minutesInCache));

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

        $this->assertEquals('./cache/remote/mf.jpg', $image->obtainFilePath());

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

        $this->assertEquals('./cache/remote/mf.jpg', $image->obtainFilePath());

    }


}
