<?php

namespace Tests\Homie\Gpio\Adapter;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use Homie\Arduino\SerialEvent;
use Homie\Gpio\Adapter\Arduino;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\Gpio\Pin;
use Homie\Gpio\PinsCollection;

class ArdunoTest extends TestCase
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

        $this->subject = new Arduino();
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testGetPins()
    {
        $actual = $this->subject->loadPins();

        $this->assertInstanceOf(PinsCollection::class, $actual);
    }


    public function testUpdate()
    {
        $gpioId = 10;

        $pin = new Pin();
        $pin->setPhysicalId($gpioId);
        $pin->setMode(Pin::DIRECTION_OUT);
        $pin->setValue(1);

        $event = new SerialEvent(SerialEvent::DIGITAL, $pin->getPhysicalId(), 1);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($event);

        $this->subject->updatePin($pin);
    }
}
