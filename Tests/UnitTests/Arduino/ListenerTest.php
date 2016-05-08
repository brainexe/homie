<?php

namespace Tests\Homie\Arduino;

use Homie\Arduino\Device\Serial;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Homie\Arduino\Listener;
use Homie\Arduino\SerialEvent;

/**
 * @covers Homie\Arduino\Listener
 */
class ListenerTest extends TestCase
{

    /**
     * @var Listener
     */
    private $subject;

    /**
     * @var Serial|MockObject
     */
    private $serial;

    public function setUp()
    {
        $this->serial  = $this->getMockWithoutInvokingTheOriginalConstructor(Serial::class);
        $this->subject = new Listener($this->serial);
    }

    public function testHandleEvent()
    {
        $action = 'a';
        $pin    = 12;
        $value  = 2;

        $event = new SerialEvent($action, $pin, $value);

        $this->serial
            ->expects($this->once())
            ->method('sendSerial')
            ->with($event);

        $this->subject->handleEvent($event);
    }
}
