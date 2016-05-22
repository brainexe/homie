<?php

namespace Tests\Homie\Webcam;

use Homie\Webcam\WebcamEvent;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Homie\Webcam\WebcamEvent
 */
class WebcamEventTest extends TestCase
{

    public function testConstruct()
    {
        $event = new WebcamEvent('name', WebcamEvent::TAKE_VIDEO, 10);

        $this->assertEquals(WebcamEvent::TAKE_VIDEO, $event->getEventName());
        $this->assertEquals(10, $event->getDuration());
        $this->assertEquals('name', $event->getName());
    }
}
