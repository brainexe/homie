<?php

namespace Tests\Homie\Media;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\InputControl\Event;
use Homie\Media\InputControl;
use Homie\Media\SoundEvent;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Homie\Media\InputControl
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
        $this->subject         = new InputControl();
        $this->subject->setEventDispatcher($this->eventDispatcher);
    }

    public function testGetSubscribedEvents()
    {
        $actual = $this->subject->getSubscribedEvents();
        $this->assertInternalType('array', $actual);
    }

    public function testPlaySound()
    {
        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($this->isInstanceOf(SoundEvent::class));

        $event = new Event();
        $this->subject->play($event);
    }
}
