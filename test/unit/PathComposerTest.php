<?php
require_once 'PathComposer.php';
require_once 'Configuration.php';
require_once 'Image.php';

class PathComposerTest extends PHPUnit_Framework_TestCase {

    public function testInstantiation() {
        $this->assertInstanceOf('PathComposer', new PathComposer(new Configuration(array('w' => 10)), new Image('')));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInstantiationWithoutDependencies() {
        $this->assertInstanceOf('PathComposer', new PathComposer(null, null));
    }

    public function testComposerWithoutDimensions() {
        $settings = array('output-filename' => 'output.jpg');
        $imagePath = 'images/dog.jpg';

        $composer = new PathComposer(new Configuration($settings), new Image($imagePath));

        $this->assertEquals('output.jpg', $composer->compose());
    }

    public function testComposerWithWidth() {
        $settings = array('w' => 10);
        $imagePath = 'images/dog.jpg';

        $composer = new PathComposer(new Configuration($settings), new Image($imagePath));

        $this->assertEquals('./cache/6e58805ff4763cba92c7e45811fcf4df_w10_sc.jpg', $composer->compose());
    }

    public function testComposerWithHeight() {
        $settings = array('h' => 10);
        $imagePath = 'images/dog.jpg';

        $composer = new PathComposer(new Configuration($settings), new Image($imagePath));

        $this->assertEquals('./cache/6e58805ff4763cba92c7e45811fcf4df_h10_sc.jpg', $composer->compose());
    }

    public function testComposerWithWidthAndHeight() {
        $settings = array('w'=>10, 'h' => 10);
        $imagePath = 'images/dog.jpg';

        $composer = new PathComposer(new Configuration($settings), new Image($imagePath));

        $this->assertEquals('./cache/6e58805ff4763cba92c7e45811fcf4df_w10_h10_sc.jpg', $composer->compose());
    }

    public function testComposerWithCrop() {
        $settings = array('w'=>10, 'h' => 10, 'crop'=>true);
        $imagePath = 'images/dog.jpg';

        $composer = new PathComposer(new Configuration($settings), new Image($imagePath));

        $this->assertEquals('./cache/6e58805ff4763cba92c7e45811fcf4df_w10_h10_cp_sc.jpg', $composer->compose());
    }

    public function testComposerWithScale() {
        $settings = array('w'=>10, 'h' => 10, 'scale'=>true);
        $imagePath = 'images/dog.jpg';

        $composer = new PathComposer(new Configuration($settings), new Image($imagePath));

        $this->assertEquals('./cache/6e58805ff4763cba92c7e45811fcf4df_w10_h10_sc.jpg', $composer->compose());
    }

    public function testComposerWithScaleFalse() {
        $settings = array('w'=>10, 'h' => 10, 'scale'=>false);
        $imagePath = 'images/dog.jpg';

        $composer = new PathComposer(new Configuration($settings), new Image($imagePath));

        $this->assertEquals('./cache/6e58805ff4763cba92c7e45811fcf4df_w10_h10.jpg', $composer->compose());
    }


}