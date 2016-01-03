<?php

namespace Tests\Homie\IFTTT;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use Homie\IFTTT\Controller;
use Homie\IFTTT\IFTTTEvent;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Symfony\Component\HttpFoundation\Request;

class ControllerTest extends TestCase
{

    /**
     * @var Controller
     */
    private $subject;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->dispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new Controller();
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testHandleEvent()
    {
        $eventName = 'my-test';

        $event = new IFTTTEvent(IFTTTEvent::ACTION, $eventName);

        $request = new Request();
        $request->query->set('event', $eventName);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($event);

        $actual = $this->subject->action($request);

        $this->assertTrue($actual);
    }
}
