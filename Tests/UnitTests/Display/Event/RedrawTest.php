<?php

namespace Tests\Homie\Display\Event;

use Homie\Display\Event\Redraw;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Homie\Display\Event\Redraw
 */
class RedrawTest extends TestCase
{

    public function testRender()
    {
        $displayId = 42;
        $event = new Redraw($displayId);

        $this->assertEquals($displayId, $event->getDisplayId());
    }
}
