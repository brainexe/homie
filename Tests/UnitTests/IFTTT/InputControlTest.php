<?php

namespace Tests\Homie\IFTTT;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\InputControl\Event;
use Homie\IFTTT\Controller;
use Homie\IFTTT\IFTTTEvent;
use Homie\IFTTT\InputControl;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\HttpFoundation\Request;

class InputControlTest extends TestCase
{

    /**
     * @var InputControl
     */
    private $subject;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->dispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new InputControl();
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testGetSubscribedEvents()
    {
        $actualResult = $this->subject->getSubscribedEvents();
        $this->assertInternalType('array', $actualResult);
    }

    public function testHandleEvent()
    {
        $eventName = 'my-test';

        $triggerEvent = new IFTTTEvent(IFTTTEvent::TRIGGER, $eventName);

        $event = new Event();
        $event->match = $eventName;

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($triggerEvent);

        $this->subject->trigger($event);
    }
}
