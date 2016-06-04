<?php

namespace Tests\Homie\Display\Devices;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use Homie\Arduino\SerialEvent;
use Homie\Display\Devices\Arduino;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Homie\Display\Devices\Arduino
 */
class ArduinoTest extends TestCase
{

    /**
     * @var Arduino
     */
    private $subject;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->dispatcher = $this->createMock(EventDispatcher::class);

        $this->subject = new Arduino($this->dispatcher);
    }

    public function testOutput()
    {
        $string = "foo";
        $pin    = 1;

        $event = new SerialEvent(SerialEvent::LCD, $pin, $string);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($event);

        $this->subject->display($string);
    }
}
