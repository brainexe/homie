<?php

namespace Tests\Raspberry\Arduino;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Raspberry\Arduino\Listener;
use Raspberry\Arduino\Serial;
use Raspberry\Arduino\SerialEvent;

/**
 * @covers Raspberry\Arduino\Listener
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
        $this->serial = $this->getMock(Serial::class, [], [], '', false);
        $this->subject    = new Listener($this->serial);
    }

    public function testGetSubscribedEvents()
    {
        $actualResult = $this->subject->getSubscribedEvents();
        $this->assertInternalType('array', $actualResult);
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
