<?php

class ConfigurationTest extends PHPUnit_Framework_TestCase {

    private $minimum = array(
        'output-filename' => 'out'
    );

    private $defaults = array(
        'crop' => false,
        'scale' => 'false',
        'thumbnail' => false,
        'maxOnly' => false,
        'canvas-color' => 'transparent',
        'output-filename' => 'out',
        'cacheFolder' => './cache/',
        'remoteFolder' => './cache/remote/',
        'quality' => 90,
        'cache_http_minutes' => 20,
        'w' => null,
        'h' => null
    );

    /**
     * @expectedException InvalidArgumentException
     */
    public function testWithoutOptions()
    {
        new Configuration();
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testNullOptions() {
        $configuration = new Configuration(null);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testBelowMinimumOptions() {
        $opts = array();
        $configuration = new Configuration($opts);
    }

    public function testMinimumOptionsDefaults() {
        $configuration = new Configuration($this->minimum);
        $asHash = $configuration->asHash();

        $this->assertEquals($this->defaults, $asHash);
    }

    public function testDefaultsNotOverwriteConfiguration() {

        $opts = array(
            'thumbnail' => true,
            'maxOnly' => true,
            'w' => 10
        );

        $configuration = new Configuration($opts);
        $configured = $configuration->asHash();

        $this->assertTrue($configured['thumbnail']);
        $this->assertTrue($configured['maxOnly']);
    }


    public function testObtainCache() {
        $configuration = new Configuration($this->minimum);

        $this->assertEquals('./cache/', $configuration->obtainCacheRootPath());
    }

    public function testObtainRemote() {
        $configuration = new Configuration($this->minimum);

        $this->assertEquals('./cache/remote/', $configuration->obtainRemoteRootPath());
    }

    public function testObtainConvertPath() {
        $configuration = new Configuration($this->minimum);

        $this->assertEquals('convert', $configuration->obtainConvertPath());
    }

    public function testMaxOnly() {
        $configuration = new Configuration($this->minimum);

        $this->assertEquals(false, $configuration->obtainMaxOnly());
    }

    public function testQuality() {
        $configuration = new Configuration($this->minimum);

        $this->assertEquals(90, $configuration->obtainQuality());
    }

    public function testCanvasColor() {
        $configuration = new Configuration($this->minimum);

        $this->assertEquals('transparent', $configuration->obtainCanvasColor());
    }

    public function testObtainCrop() {
        $configuration = new Configuration($this->minimum);

        $this->assertEquals(false, $configuration->obtainCrop());
    }

    public function testObtainScale() {
        $configuration = new Configuration($this->minimum);

        $this->assertEquals('false', $configuration->obtainScale());
    }

    public function testOutputFileName() {
        $configuration = new Configuration($this->minimum);

        $this->assertEquals('out', $configuration->obtainOutputFilename());
    }
}


