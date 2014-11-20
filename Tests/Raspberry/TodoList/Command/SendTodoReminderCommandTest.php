<?php

namespace Tests\Raspberry\TodoList\Command\SendTodoReminderCommand;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\TodoList\Command\SendTodoReminderCommand;
use Raspberry\TodoList\TodoReminder;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @Covers Raspberry\TodoList\Command\SendTodoReminderCommand
 */
class SendTodoReminderCommandTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var SendTodoReminderCommand
	 */
	private $_subject;

	/**
	 * @var TodoReminder|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockTodoReminder;

	public function setUp() {
		$this->_mockTodoReminder = $this->getMock(TodoReminder::class, [], [], '', false);
		$this->_subject = new SendTodoReminderCommand($this->_mockTodoReminder);
	}

	public function testExecute() {
		$application = new Application();
        $application->add($this->_subject);

        $command = $application->find('todo:reminder');
        $commandTester = new CommandTester($command);

		$this->_mockTodoReminder
			->expects($this->once())
			->method('sendNotification');

        $commandTester->execute(['command' => $command->getName()]);
	}

}
