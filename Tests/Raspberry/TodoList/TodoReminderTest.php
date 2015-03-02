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
    private $todoList;

    /**
     * @var EventDispatcher|MockObject
     */
    private $eventDispatcher;

    public function setUp()
    {
        $this->todoList        = $this->getMock(TodoList::class, [], [], '', false);
        $this->eventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

        $this->subject = new TodoReminder($this->todoList);
        $this->subject->setEventDispatcher($this->eventDispatcher);
    }

    public function testSendNotificationShouldDoNothingWhenClosed()
    {
        $todos = [];

        $todo = $todos[] = new TodoItemVO();
        $todo->status = TodoItemVO::STATUS_COMPLETED;

        $this->todoList
            ->expects($this->once())
            ->method('getList')
            ->willReturn($todos);

        $this->eventDispatcher
            ->expects($this->never())
            ->method('dispatchInBackground');

        $this->subject->sendNotification();
    }

    public function testSendNotification()
    {
        $todos = [];

        $todoPending = $todos[] = new TodoItemVO();
        $todoPending->status = TodoItemVO::STATUS_PENDING;

        $todoProgress = $todos[] = new TodoItemVO();
        $todoProgress->status = TodoItemVO::STATUS_PROGRESS;
        $todoProgress2 = $todos[] = new TodoItemVO();
        $todoProgress2->status = TodoItemVO::STATUS_PROGRESS;

        $todoUnknown = $todos[] = new TodoItemVO();
        $todoUnknown->status = 'unknown';

        $this->todoList
            ->expects($this->once())
            ->method('getList')
            ->willReturn($todos);

        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatchInBackground');

        //TODO check for exact event

        $this->subject->sendNotification();
    }
}
