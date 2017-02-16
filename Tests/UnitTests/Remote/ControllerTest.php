<?php

namespace Tests\Homie\Remote;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use Homie\Remote\Controller;
use Homie\Remote\Event\ReceivedEvent;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit\Framework\TestCase;
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

        $this->subject = new Controller();
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testAction()
    {
        $code    = 'code';
        $event   = new ReceivedEvent($code);
        $request = new Request();

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($event);

        $actual = $this->subject->action($request, $code);

        $this->assertTrue($actual);
    }
}
