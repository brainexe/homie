<?php

namespace Tests\Homie\Dashboard\Widgets;

use PHPUnit_Framework_TestCase as TestCase;
use Homie\Dashboard\Widgets\WidgetMetadataVo;

class WidgetMetadataVoTest extends TestCase
{

    public function testSetSize()
    {
        $subject = new WidgetMetadataVo('id', []);

        $this->assertEquals(2, $subject->height);
        $this->assertEquals(4, $subject->width);

        $subject->setSize(6, 3);

        $this->assertEquals(3, $subject->height);
        $this->assertEquals(6, $subject->width);
    }

    public function testAddTitle()
    {
        $subject = new WidgetMetadataVo('id', []);

        $this->assertCount(2, $subject->parameters);

        $subject->addTitle();

        $this->assertCount(3, $subject->parameters);
    }
}
