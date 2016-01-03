<?php

namespace Tests\Homie\IFTTT;

use Homie\IFTTT\Trigger;
use Homie\IFTTT\IFTTTEvent;
use Homie\IFTTT\Listener;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

class ListenerTest extends TestCase
{

    /**
     * @var Listener
     */
    private $subject;

    /**
     * @var Trigger|MockObject
     */
    private $action;

    public function setUp()
    {
        $this->action  = $this->getMock(Trigger::class, [], [], '', false);
        $this->subject = new Listener($this->action);
    }

    public function testGetSubscribedEvents()
    {
        $actualResult = $this->subject->getSubscribedEvents();
        $this->assertInternalType('array', $actualResult);
    }

    public function testHandleEvent()
    {
        $eventName = 'my-test';
        $event = new IFTTTEvent(IFTTTEvent::TRIGGER, $eventName);

        $this->action
            ->expects($this->once())
            ->method('trigger')
            ->with($eventName);

        $this->subject->callTrigger($event);
    }
}
