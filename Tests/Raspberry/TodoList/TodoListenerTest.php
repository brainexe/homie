<?php

namespace Tests\Raspberry\TodoList\TodoListener;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\TodoList\TodoListener;
use BrainExe\Core\EventDispatcher\EventDispatcher;

/**
 * @Covers Raspberry\TodoList\TodoListener
 */
class TodoListenerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var TodoListener
	 */
	private $_subject;

	/**
	 * @var EventDispatcher|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockEventDispatcher;


	public function setUp() {
		parent::setUp();

		$this->_mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);

		$this->_subject = new TodoListener();
		$this->_subject->setEventDispatcher($this->_mockEventDispatcher);
	}

	public function testGetSubscribedEvents() {
		$actual_result = $this->_subject->getSubscribedEvents();
		$this->assertInternalType('array', $actual_result);
	}

	public function testHandleAddEvent() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$this->_subject->handleAddEvent($event);
	}

}
