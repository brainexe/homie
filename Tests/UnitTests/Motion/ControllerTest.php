<?php

namespace Tests\Homie\Motion;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use Homie\Motion\Controller;
use Homie\Motion\MotionEvent;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

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

    public function testAdd()
    {
        $event = new MotionEvent(MotionEvent::MOTION);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->willReturn($event);

        $actual = $this->subject->add();

        $this->assertTrue($actual);
    }
}
