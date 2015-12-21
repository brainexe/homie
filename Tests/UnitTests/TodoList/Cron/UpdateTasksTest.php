<?php

namespace Tests\Homie\TodoList\Cron;

use BrainExe\Core\Util\Time;
use Homie\TodoList\Cron\UpdateTasks;
use Homie\TodoList\TodoList;
use Homie\TodoList\VO\TodoItemVO;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers Homie\TodoList\Cron\UpdateTasks
 */
class UpdateTasksTest extends TestCase
{
    /**
     * @var UpdateTasks
     */
    private $subject;

    /**
     * @var TodoList|MockObject
     */
    private $todoList;

    /**
     * @var Time|MockObject
     */
    private $time;

    public function setUp()
    {
        $this->todoList = $this->getMock(TodoList::class, [], [], '', false);
        $this->time     = $this->getMock(Time::class);

        $this->subject = new UpdateTasks($this->todoList);
        $this->subject->setTime($this->time);
    }

    public function testExecuteEmpty()
    {
        $this->todoList
            ->expects($this->once())
            ->method('getList')
            ->willReturn([]);

        $application = new Application();
        $application->add($this->subject);
        $commandTester = new CommandTester($this->subject);
        $commandTester->execute([]);
    }

    public function testExecute()
    {
        $task = new TodoItemVO();
        $task->todoId = 11880;
        $task->status = TodoItemVO::STATUS_PENDING;
        $task->cronExpression = '@daily';

        $this->time
            ->expects($this->once())
            ->method('now')
            ->willReturn(1000000000000);

        $this->todoList
            ->expects($this->once())
            ->method('getList')
            ->willReturn([$task]);
        $this->todoList
            ->expects($this->once())
            ->method('editItem')
            ->with(11880, [
                'status' => TodoItemVO::STATUS_OPEN
            ]);

        $application = new Application();
        $application->add($this->subject);
        $commandTester = new CommandTester($this->subject);
        $commandTester->execute([]);
    }
}
