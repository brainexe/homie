<?php

namespace Tests\Homie\IFTTT;

use Homie\IFTTT\Event\TriggerEvent;
use Homie\IFTTT\Trigger;
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
        $this->action  = $this->createMock(Trigger::class);
        $this->subject = new Listener($this->action);
    }

    public function testHandleEvent()
    {
        $eventName = 'my-test';
        $event = new TriggerEvent($eventName);

        $this->action
            ->expects($this->once())
            ->method('trigger')
            ->with($eventName);

        $this->subject->callTrigger($event);
    }
}
