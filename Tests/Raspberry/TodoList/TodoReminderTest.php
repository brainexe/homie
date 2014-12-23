<?php

namespace Tests\Raspberry\TodoList\TodoReminder;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\TodoList\TodoReminder;
use Raspberry\TodoList\TodoList;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use Raspberry\TodoList\VO\TodoItemVO;

/**
 * @Covers Raspberry\TodoList\TodoReminder
 */
class TodoReminderTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var TodoReminder
     */
    private $subject;

    /**
     * @var TodoList|MockObject
     */
    private $mockTodoList;

    /**
     * @var EventDispatcher|MockObject
     */
    private $mockEventDispatcher;

    public function setUp()
    {
        $this->mockTodoList = $this->getMock(TodoList::class, [], [], '', false);
        $this->mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new TodoReminder($this->mockTodoList);
        $this->subject->setEventDispatcher($this->mockEventDispatcher);
    }

    public function testSendNotificationShouldDoNothingWhenClosed()
    {
        $todos = [];

        $todo = $todos[] = new TodoItemVO();
        $todo->status = TodoItemVO::STATUS_COMPLETED;

        $this->mockTodoList
        ->expects($this->once())
        ->method('getList')
        ->willReturn($todos);

        $this->mockEventDispatcher
        ->expects($this->never())
        ->method('dispatchInBackground');

        $this->subject->sendNotification();
    }

    public function testSendNotification()
    {
        $todos = [];

        $todo_pending = $todos[] = new TodoItemVO();
        $todo_pending->status = TodoItemVO::STATUS_PENDING;

        $todo_progress = $todos[] = new TodoItemVO();
        $todo_progress->status = TodoItemVO::STATUS_PROGRESS;

        $todo_unknown = $todos[] = new TodoItemVO();
        $todo_unknown->status = 'unknown';

        $this->mockTodoList
        ->expects($this->once())
        ->method('getList')
        ->willReturn($todos);

        $this->mockEventDispatcher
        ->expects($this->once())
        ->method('dispatchInBackground');
      //TODO check for exact event

        $this->subject->sendNotification();
    }
}
