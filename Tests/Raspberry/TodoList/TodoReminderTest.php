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
class TodoReminderTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var TodoReminder
	 */
	private $_subject;

	/**
	 * @var TodoList|MockObject
	 */
	private $_mockTodoList;

	/**
	 * @var EventDispatcher|MockObject
	 */
	private $_mockEventDispatcher;

	public function setUp() {
		$this->_mockTodoList = $this->getMock(TodoList::class, [], [], '', false);
		$this->_mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

		$this->_subject = new TodoReminder($this->_mockTodoList);
		$this->_subject->setEventDispatcher($this->_mockEventDispatcher);
	}

	public function testSendNotificationShouldDoNothingWhenClosed() {
		$todos = [];

		$todo = $todos[] = new TodoItemVO();
		$todo->status = TodoItemVO::STATUS_COMPLETED;

		$this->_mockTodoList
			->expects($this->once())
			->method('getList')
			->will($this->returnValue($todos));

		$this->_mockEventDispatcher
			->expects($this->never())
			->method('dispatchInBackground');

		$this->_subject->sendNotification();
	}

	public function testSendNotification() {
		$todos = [];

		$todo_pending = $todos[] = new TodoItemVO();
		$todo_pending->status = TodoItemVO::STATUS_PENDING;

		$todo_progress = $todos[] = new TodoItemVO();
		$todo_progress->status = TodoItemVO::STATUS_PROGRESS;

		$todo_unknown = $todos[] = new TodoItemVO();
		$todo_unknown->status = 'unknown';

		$this->_mockTodoList
			->expects($this->once())
			->method('getList')
			->will($this->returnValue($todos));

		$this->_mockEventDispatcher
			->expects($this->once())
			->method('dispatchInBackground');
			//TODO check for exact event

		$this->_subject->sendNotification();
	}

}
