<?php

require_once 'CommandComposer.php';

class CommandComposerTest extends PHPUnit_Framework_TestCase {

    public function testInstantiation() {
        $this->assertInstanceOf('CommandComposer', new CommandComposer(new Configuration(array('w' => 10))));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInstantiationWithoutConfiguration() {
        $this->assertInstanceOf('CommandComposer', new CommandComposer(null));
    }

    public function testComposeDefaultCommandWithWidth() {
        $composer = new CommandComposer(new Configuration(array('w' => 10)));
        $imagePath = 'input-path';
        $outputPath = 'output-path';
        $command = $composer->defaultCommand($imagePath, $outputPath);

        $this->assertEquals("convert 'input-path' -thumbnail 10 -quality '90' 'output-path'", $command);
    }

    public function testComposeDefaultCommandWithHeight() {
        $composer = new CommandComposer(new Configuration(array('h' => 20, 'w' => 10)));
        $imagePath = 'input-path';
        $outputPath = 'output-path';
        $command = $composer->defaultCommand($imagePath, $outputPath);

        $this->assertEquals("convert 'input-path' -thumbnail x10 -quality '90' 'output-path'", $command);
    }

    public function testComposeDefaultCommandWithoutDimensions() {
        $composer = new CommandComposer(new Configuration(array('output-filename' => 'out')));
        $imagePath = 'input-path';
        $outputPath = 'output-path';
        $command = $composer->defaultCommand($imagePath, $outputPath);

        $this->assertEquals("convert 'input-path' -thumbnail  -quality '90' 'output-path'", $command);
    }

    public function testComposeWithCropCommandWithWidthPanoramic() {
        $composer = new CommandComposer(new Configuration(array('w' => 10)));
        $imagePath = 'input-path';
        $outputPath = 'output-path';
        $isPanoramic = true;
        $command = $composer->withCropCommand($imagePath, $outputPath, $isPanoramic);

        $this->assertEquals("convert 'input-path' -resize '10' -size '10x' xc:'transparent' +swap -gravity center -composite -quality '90' 'output-path'", $command);
    }

    public function testComposeWithCropCommandWithWidthPortrait() {
        $composer = new CommandComposer(new Configuration(array('w' => 10)));
        $imagePath = 'input-path';
        $outputPath = 'output-path';
        $isPanoramic = false;
        $command = $composer->withCropCommand($imagePath, $outputPath, $isPanoramic);

        $this->assertEquals("convert 'input-path' -resize 'x' -size '10x' xc:'transparent' +swap -gravity center -composite -quality '90' 'output-path'", $command);
    }

    public function testComposeWithCropCommandWithHeightPanoramic() {
        $composer = new CommandComposer(new Configuration(array('h' => 20, 'w' => 10)));
        $imagePath = 'input-path';
        $outputPath = 'output-path';
        $isPanoramic = true;
        $command = $composer->withCropCommand($imagePath, $outputPath, $isPanoramic);

        $this->assertEquals("convert 'input-path' -resize '10' -size '10x20' xc:'transparent' +swap -gravity center -composite -quality '90' 'output-path'", $command);
    }

    public function testComposeWithCropCommandWithHeightPortrait() {
        $composer = new CommandComposer(new Configuration(array('h' => 20, 'w' => 10)));
        $imagePath = 'input-path';
        $outputPath = 'output-path';
        $isPanoramic = false;
        $command = $composer->withCropCommand($imagePath, $outputPath, $isPanoramic);

        $this->assertEquals("convert 'input-path' -resize 'x20' -size '10x20' xc:'transparent' +swap -gravity center -composite -quality '90' 'output-path'", $command);
    }

    public function testComposeWithCropCommandWithoutDimensions() {
        $composer = new CommandComposer(new Configuration(array('output-filename' => 'out')));
        $imagePath = 'input-path';
        $outputPath = 'output-path';
        $isPanoramic = true;
        $command = $composer->withCropCommand($imagePath, $outputPath, $isPanoramic);

        $this->assertEquals("convert 'input-path' -resize '' -size 'x' xc:'transparent' +swap -gravity center -composite -quality '90' 'output-path'", $command);
    }


}