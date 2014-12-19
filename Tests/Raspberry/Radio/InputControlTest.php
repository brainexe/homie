<?php

namespace Tests\Raspberry\Radio;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\InputControl\Event;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Raspberry\Radio\InputControl;
use Raspberry\Radio\RadioChangeEvent;
use Raspberry\Radio\Radios;
use Raspberry\Radio\VO\RadioVO;

/**
 * @Covers Raspberry\Radio\InputControl
 */
class InputControlTest extends TestCase
{

    /**
     * @var InputControl
     */
    private $subject;

    /**
     * @var Radios|MockObject
     */
    private $mockRadios;

    /**
     * @var EventDispatcher|MockObject
     */
    private $mockEventDispatcher;

    public function setUp()
    {
        $this->mockRadios          = $this->getMock(Radios::class, [], [], '', false);
        $this->mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new InputControl($this->mockRadios);
        $this->subject->setEventDispatcher($this->mockEventDispatcher);
    }
    public function testGetSubscribedEvents()
    {
        $actualResult = $this->subject->getSubscribedEvents();

        $this->assertInternalType('array', $actualResult);
    }

    public function testSay()
    {
        $radioId    = 5;
        $inputEvent = new Event();
        $inputEvent->matches = ['on', $radioId];

        $radioVo = new RadioVO();
        $event   = new RadioChangeEvent($radioVo, true);

        $this->mockRadios
        ->expects($this->once())
        ->method('getRadio')
        ->with($radioId)
        ->willReturn($radioVo);
        $this->mockEventDispatcher
        ->expects($this->once())
        ->method('dispatchEvent')
        ->with($event);

        $this->subject->setRadio($inputEvent);
    }
}
