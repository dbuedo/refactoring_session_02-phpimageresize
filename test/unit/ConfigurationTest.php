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

        $this->assertEquals('./cache/', $configuration->obtainCache());
    }

    public function testObtainRemote() {
        $configuration = new Configuration($this->minimum);

        $this->assertEquals('./cache/remote/', $configuration->obtainRemote());
    }

    public function testObtainConvertPath() {
        $configuration = new Configuration($this->minimum);

        $this->assertEquals('convert', $configuration->obtainConvertPath());
    }

}


