<?php
require_once 'ImagePath.php';

class ImagePathTest extends PHPUnit_Framework_TestCase {

    public function testIsSanitizedAtInstantiation() {
        $url = 'https://www.google.com/webhp?sourceid=chrome-instant&ion=1&espv=2&ie=UTF-8#safe=off&q=php%20define%20dictionary';
        $expected = 'https://www.google.com/webhp?sourceid=chrome-instant&ion=1&espv=2&ie=UTF-8#safe=off&q=php define dictionary';

        $imagePath = new ImagePath($url);

        $this->assertEquals($expected, $imagePath->sanitizedPath());
    }

    public function testIsHttpProtocol() {
        $imagePath = new ImagePath('https://example.com');
        $this->assertTrue($imagePath->isHttpProtocol());

        $imagePath = new ImagePath('ftp://example.com');
        $this->assertFalse($imagePath->isHttpProtocol());

        $imagePath = new ImagePath(null);
        $this->assertFalse($imagePath->isHttpProtocol());

        $imagePath = new ImagePath('/absolute/local/path');
        $this->assertFalse($imagePath->isHttpProtocol());

        $imagePath = new ImagePath('relative/local/path');
        $this->assertFalse($imagePath->isHttpProtocol());
    }

    public function testObtainFileName() {
        $url = 'http://martinfowler.com/mf.jpg?query=hello&s=fowler';

        $imagePath = new ImagePath($url);

        $this->assertEquals('mf.jpg', $imagePath->obtainFileName());
    }


}
