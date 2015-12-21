<?php

namespace Tests\Homie\Radio;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\InputControl\Event;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Homie\Radio\InputControl;
use Homie\Radio\SwitchChangeEvent;
use Homie\Radio\Switches;
use Homie\Radio\VO\RadioVO;

/**
 * @covers Homie\Radio\InputControl
 */
class InputControlTest extends TestCase
{

    /**
     * @var InputControl
     */
    private $subject;

    /**
     * @var Switches|MockObject
     */
    private $radios;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->radios     = $this->getMock(Switches::class, [], [], '', false);
        $this->dispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new InputControl($this->radios);
        $this->subject->setEventDispatcher($this->dispatcher);
    }
    public function testGetSubscribedEvents()
    {
        $actualResult = $this->subject->getSubscribedEvents();

        $this->assertInternalType('array', $actualResult);
    }

    public function testSay()
    {
        $switchId   = 5;
        $inputEvent = new Event();
        $inputEvent->matches = ['on', $switchId];

        $radioVo = new RadioVO();
        $event   = new SwitchChangeEvent($radioVo, true);

        $this->radios
            ->expects($this->once())
            ->method('get')
            ->with($switchId)
            ->willReturn($radioVo);
        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($event);

        $this->subject->setSwitch($inputEvent);
    }
}
