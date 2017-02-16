<?php

namespace Tests\Homie\TodoList\Listener;

use Homie\TodoList\Listener\Add;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use Homie\TodoList\TodoListEvent;
use Homie\TodoList\VO\TodoItemVO;
use PHPUnit\Framework\TestCase;

class AddTest extends TestCase
{

    /**
     * @var Add
     */
    private $subject;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->dispatcher = $this->createMock(EventDispatcher::class);

        $this->subject = new Add();
        $this->subject->setEventDispatcher($this->dispatcher);
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
