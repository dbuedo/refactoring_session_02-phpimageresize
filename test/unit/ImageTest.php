<?php
require_once 'Image.php';

class ImagePathTest extends PHPUnit_Framework_TestCase {

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
        $localRemoteFolder = './cache/remote/';
        $url = 'http://martinfowler.com/mf.jpg?query=hello&s=fowler';
        $image = new Image($url, $localRemoteFolder);

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

        $image = new Image('http://martinfowler.com/mf.jpg?query=hello&s=fowler', './cache/remote/');
        $image->injectFileSystem($stub);

        $this->assertTrue($image->isCached($minutesInCache));

    }



}
