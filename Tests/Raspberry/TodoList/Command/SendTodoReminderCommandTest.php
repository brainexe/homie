<?php

namespace Tests\Raspberry\TodoList\Command\SendTodoReminderCommand;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\TodoList\Command\SendTodoReminderCommand;
use Raspberry\TodoList\TodoReminder;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @Covers Raspberry\TodoList\Command\SendTodoReminderCommand
 */
class SendTodoReminderCommandTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var SendTodoReminderCommand
     */
    private $subject;

    /**
     * @var TodoReminder|MockObject
     */
    private $mockTodoReminder;

    public function setUp()
    {
        $this->mockTodoReminder = $this->getMock(TodoReminder::class, [], [], '', false);
        $this->subject = new SendTodoReminderCommand($this->mockTodoReminder);
    }

    public function testExecute()
    {
        $application = new Application();
        $application->add($this->subject);

        $command = $application->find('todo:reminder');
        $commandTester = new CommandTester($command);

        $this->mockTodoReminder
        ->expects($this->once())
        ->method('sendNotification');

        $commandTester->execute(['command' => $command->getName()]);
    }
}
