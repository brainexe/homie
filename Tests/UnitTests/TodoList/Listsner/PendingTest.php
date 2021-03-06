<?php

namespace Tests\Homie\TodoList\Listener;

use BrainExe\Core\Cron\Expression;
use Homie\Espeak\EspeakEvent;
use Homie\TodoList\Exception\ItemNotFoundException;
use Homie\TodoList\Listener\Pending;
use Homie\TodoList\TodoList;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use Homie\TodoList\TodoListEvent;
use Homie\TodoList\VO\TodoItemVO;
use PHPUnit\Framework\TestCase;

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
        $this->dispatcher = $this->createMock(EventDispatcher::class);
        $this->todoList   = $this->createMock(TodoList::class);
        $this->cron       = $this->createMock(Expression::class);

        $this->subject = new Pending(
            $this->todoList,
            $this->cron
        );
        $this->subject->setEventDispatcher($this->dispatcher);
    }

    public function testHandleEditEventWithoutCronExpression(): void
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

    public function testHandleEditEventWithCronExpression(): void
    {
        $itemVo = new TodoItemVO();
        $itemVo->cronExpression = '@daily';
        $nextRun = 100000;

        $event = new TodoListEvent($itemVo, TodoListEvent::EDIT, [
            'status' => TodoItemVO::STATUS_PENDING
        ]);

        $newEvent = new TodoListEvent($itemVo, TodoListEvent::PENDING);

        $this->cron
            ->expects($this->once())
            ->method('getNextRun')
            ->with('@daily')
            ->willReturn($nextRun);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($newEvent, $nextRun);

        $this->subject->handleEditEvent($event);
    }

    public function testHandlePendingEventWithExpiredItem(): void
    {
        $itemVo = new TodoItemVO();
        $itemVo->todoId = 42;
        $itemVo->cronExpression = '* * * * *';

        $event = new TodoListEvent($itemVo, TodoListEvent::PENDING);

        $this->todoList
            ->expects($this->once())
            ->method('getItem')
            ->with($itemVo->todoId)
            ->willThrowException(new ItemNotFoundException());

        $this->dispatcher
            ->expects($this->never())
            ->method('dispatchInBackground');

        $this->subject->handlePendingEvent($event);
    }

    public function testHandlePendingEvent(): void
    {
        $itemVo = new TodoItemVO();
        $itemVo->todoId = 42;
        $itemVo->status = TodoItemVO::STATUS_PENDING;
        $itemVo->cronExpression = '* * * * *';
        $itemVo->name = 'name';

        $event = new TodoListEvent($itemVo, TodoListEvent::PENDING);

        $this->todoList
            ->expects($this->once())
            ->method('getItem')
            ->with($itemVo->todoId)
            ->willReturn($itemVo);

        $this->todoList
            ->expects($this->once())
            ->method('editItem')
            ->with($itemVo->todoId, [
                'status' => TodoItemVO::STATUS_OPEN
            ]);

        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchInBackground')
            ->with($this->isInstanceOf(EspeakEvent::class));

        $this->subject->handlePendingEvent($event);
    }
}
