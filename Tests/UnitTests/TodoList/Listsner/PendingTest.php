<?php

namespace Tests\Homie\TodoList\Listener;

use BrainExe\Core\Cron\Expression;
use Homie\TodoList\Listener\Pending;
use Homie\TodoList\TodoList;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use Homie\TodoList\TodoListEvent;
use Homie\TodoList\VO\TodoItemVO;
use PHPUnit_Framework_TestCase as TestCase;

class PendingTest extends TestCase
{

    /**
     * @var Pending
     */
    private $subject;

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    /**
     * @var TodoList|MockObject
     */
    private $todoList;

    /**
     * @var Expression|MockObject
     */
    private $cron;

    public function setUp()
    {
        $this->dispatcher = $this->getMockWithoutInvokingTheOriginalConstructor(EventDispatcher::class);
        $this->todoList   = $this->getMockWithoutInvokingTheOriginalConstructor(TodoList::class);
        $this->cron       = $this->getMockWithoutInvokingTheOriginalConstructor(Expression::class);

        $this->subject = new Pending(
            $this->todoList,
            $this->cron
        );
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testHandleEditEventWithoutCronExpression()
    {
        $itemVo = new TodoItemVO();
        $itemVo->cronExpression = '';

        $event = new TodoListEvent($itemVo, TodoListEvent::EDIT, [
            'status' => TodoItemVO::STATUS_OPEN
        ]);

        $this->dispatcher
            ->expects($this->never())
            ->method('dispatchInBackground');

        $this->subject->handleEditEvent($event);
    }
}
