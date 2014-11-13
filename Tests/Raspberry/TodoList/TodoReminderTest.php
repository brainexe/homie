<?php

namespace Tests\Raspberry\TodoList\TodoReminder;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\TodoList\TodoReminder;
use Raspberry\TodoList\TodoList;
use BrainExe\Core\EventDispatcher\EventDispatcher;

/**
 * @Covers Raspberry\TodoList\TodoReminder
 */
class TodoReminderTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var TodoReminder
	 */
	private $_subject;

	/**
	 * @var TodoList|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockTodoList;

	/**
	 * @var EventDispatcher|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockEventDispatcher;

	public function setUp() {
		$this->_mockTodoList = $this->getMock(TodoList::class, [], [], '', false);
		$this->_mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);
		$this->_subject = new TodoReminder($this->_mockTodoList);
		$this->_subject->setEventDispatcher($this->_mockEventDispatcher);
	}

	public function testSendNotification() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$this->_subject->sendNotification();
	}

}
