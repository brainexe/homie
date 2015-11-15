<?php

namespace Tests\Homie\TodoList;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\TodoList\Listener;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use Homie\TodoList\TodoListEvent;
use Homie\TodoList\VO\TodoItemVO;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Homie\TodoList\Listener
 */
class ListenerTest extends TestCase
{

    /**
     * @var Listener
     */
    private $subject;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        parent::setUp();

        $this->dispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new Listener();
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testGetSubscribedEvents()
    {
        $actualResult = $this->subject->getSubscribedEvents();
        $this->assertInternalType('array', $actualResult);
    }

    public function testHandleAddEventWithOutDeadline()
    {
        $itemVo = new TodoItemVO();
        $itemVo->deadline = 0;

        $event = new TodoListEvent($itemVo, TodoListEvent::ADD);

        $this->dispatcher
            ->expects($this->never())
            ->method('dispatchInBackground');

        $this->subject->handleAddEvent($event);
    }

    public function testHandleAddEventWithDeadline()
    {
        $itemVo = new TodoItemVO();
        $itemVo->deadline = $deadline = 10;

        $event = new TodoListEvent($itemVo, TodoListEvent::ADD);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($this->anything(), $deadline);

        $this->subject->handleAddEvent($event);
    }
}
