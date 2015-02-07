<?php

namespace Tests\Raspberry\Arduino;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Raspberry\Arduino\Listener;
use Raspberry\Arduino\Serial;
use Raspberry\Arduino\SerialEvent;

/**
 * @Covers Raspberry\Arduino\Listener
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
    private $mockSerial;

    public function setUp()
    {
        $this->mockSerial = $this->getMock(Serial::class, [], [], '', false);
        $this->subject    = new Listener($this->mockSerial);
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

        $this->mockSerial
            ->expects($this->once())
            ->method('sendSerial')
            ->with($event);

        $this->subject->handleEvent($event);
    }

}
