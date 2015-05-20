<?php

namespace Tests\Homie\TodoList\Command\SendTodoReminderCommand;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\TodoList\Command\SendTodoReminderCommand;
use Homie\TodoList\TodoReminder;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers Homie\TodoList\Command\SendTodoReminderCommand
 */
class SendTodoReminderCommandTest extends TestCase
{

    /**
     * @var SendTodoReminderCommand
     */
    private $subject;

    /**
     * @var TodoReminder|MockObject
     */
    private $todoReminder;

    public function setUp()
    {
        $this->todoReminder = $this->getMock(TodoReminder::class, [], [], '', false);
        $this->subject = new SendTodoReminderCommand($this->todoReminder);
    }

    public function testExecute()
    {
        $application = new Application();
        $application->add($this->subject);

        $command = $application->find('todo:reminder');
        $commandTester = new CommandTester($command);

        $this->todoReminder
            ->expects($this->once())
            ->method('sendNotification');

        $commandTester->execute(['command' => $command->getName()]);
    }
}
