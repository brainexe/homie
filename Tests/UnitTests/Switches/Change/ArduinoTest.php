<?php

namespace Tests\Homie\Switches\Change;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use Homie\Arduino\SerialEvent;
use Homie\Switches\Change\Arduino;
use Homie\Switches\VO\ArduinoSwitchVO;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Homie\Switches\Change\Arduino
 */
class ArduinoTest extends TestCase
{

    /**
     * @var Arduino
     */
    private $subject;

    public function setUp()
    {
        $this->dispatcher = $this->createMock(EventDispatcher::class);

        $this->subject = new Arduino();
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function testSetStatus()
    {
        $radioVo = new ArduinoSwitchVO();
        $radioVo->pin = $pin = 2;
        $status = 1;

        $event = new SerialEvent(SerialEvent::DIGITAL, $pin, $status);
        $this
            ->dispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($event);

        $this->subject->setStatus($radioVo, $status);
    }
}
