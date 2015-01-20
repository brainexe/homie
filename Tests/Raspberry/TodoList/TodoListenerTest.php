<?php

namespace Tests\Raspberry\TodoList\TodoListener;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

use Raspberry\TodoList\TodoListener;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use Raspberry\TodoList\TodoListEvent;
use Raspberry\TodoList\VO\TodoItemVO;

/**
 * @Covers Raspberry\TodoList\TodoListener
 */
class TodoListenerTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var TodoListener
     */
    private $subject;

    /**
     * @var EventDispatcher|MockObject
     */
    private $mockEventDispatcher;


    public function setUp()
    {
        parent::setUp();

        $this->mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new TodoListener();
        $this->subject->setEventDispatcher($this->mockEventDispatcher);
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

        $this->mockEventDispatcher
            ->expects($this->never())
            ->method('dispatchInBackground');

        $this->subject->handleAddEvent($event);
    }

    public function testHandleAddEventWithDeadline()
    {
        $itemVo = new TodoItemVO();
        $itemVo->deadline = $deadline = 10;

        $event = new TodoListEvent($itemVo, TodoListEvent::ADD);

        $this->mockEventDispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($this->anything(), $deadline);

        $this->subject->handleAddEvent($event);
    }
}
