<?php

namespace Tests\Raspberry\Sensors;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\InputControl\Event;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Raspberry\Espeak\EspeakEvent;
use Raspberry\Espeak\EspeakVO;
use Raspberry\Sensors\InputControl;

/**
 * @covers Raspberry\Sensors\InputControl
 */
class InputControlTest extends TestCase
{

    /**
     * @var InputControl
     */
    private $subject;

    /**
     * @var EventDispatcher|MockObject
     */
    private $eventDispatcher;

    public function setUp()
    {
        $this->eventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new InputControl();
        $this->subject->setEventDispatcher($this->eventDispatcher);
    }

    public function testGetSubscribedEvents()
    {
        $actualResult = $this->subject->getSubscribedEvents();

        $this->assertInternalType('array', $actualResult);
    }

    public function testEspeakSensor()
    {
        $inputEvent = new Event();
        $inputEvent->match = $sensorId = 'sensorID';

        $event = new EspeakEvent(new EspeakVO($sensorId));

        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($event);

        $this->subject->espeakSensor($inputEvent);
    }
}
