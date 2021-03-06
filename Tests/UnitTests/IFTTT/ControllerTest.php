<?php

namespace Tests\Homie\IFTTT;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use Homie\IFTTT\Controller;
use Homie\IFTTT\Event\ActionEvent;
use PHPUnit\Framework\TestCase;
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
        $this->dispatcher = $this->createMock(EventDispatcher::class);

        $this->subject = new Controller($this->dispatcher);
    }

    public function testHandleEvent()
    {
        $eventName = 'my-test';

        $event = new ActionEvent($eventName);

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
