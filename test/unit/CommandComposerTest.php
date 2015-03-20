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

    public function testComposeCommandWithoutDimensionsIsDefault() {
        $composer = new CommandComposer(new Configuration(array('output-filename' => 'out')));
        $imagePath = 'input-path';
        $outputPath = 'output-path';
        $command = $composer->composeCommand($imagePath, $outputPath, null);

        $this->assertEquals("convert 'input-path' -thumbnail  -quality '90' 'output-path'", $command);
    }

    public function testComposeCropCommandPanoramic() {
        $composer = new CommandComposer(new Configuration(array('h' => 20, 'w' => 10)));
        $imagePath = 'input-path';
        $outputPath = 'output-path';
        $isPanoramic = true;
        $command = $composer->composeCommand($imagePath, $outputPath, $isPanoramic);

        $this->assertEquals("convert 'input-path' -resize '10' -size '10x20' xc:'transparent' +swap -gravity center -composite -quality '90' 'output-path'", $command);
    }

    public function testComposeCropCommandPortrait() {
        $composer = new CommandComposer(new Configuration(array('h' => 20, 'w' => 10)));
        $imagePath = 'input-path';
        $outputPath = 'output-path';
        $isPanoramic = false;
        $command = $composer->composeCommand($imagePath, $outputPath, $isPanoramic);

        $this->assertEquals("convert 'input-path' -resize 'x20' -size '10x20' xc:'transparent' +swap -gravity center -composite -quality '90' 'output-path'", $command);
    }

    public function testComposeScaleCommandPanoramic() {
        $composer = new CommandComposer(new Configuration(array('h' => 20, 'w' => 10, 'scale'=> true)));
        $imagePath = 'input-path';
        $outputPath = 'output-path';
        $isPanoramic = true;
        $command = $composer->composeCommand($imagePath, $outputPath, $isPanoramic);

        $this->assertEquals("convert 'input-path' -resize '10' -quality '90' 'output-path'", $command);
    }

    public function testComposeScaleCommandPortrait() {
        $composer = new CommandComposer(new Configuration(array('h' => 20, 'w' => 10, 'scale'=> true)));
        $imagePath = 'input-path';
        $outputPath = 'output-path';
        $isPanoramic = false;
        $command = $composer->composeCommand($imagePath, $outputPath, $isPanoramic);

        $this->assertEquals("convert 'input-path' -resize 'x20' -quality '90' 'output-path'", $command);
    }

}