<?php

namespace Tests\Raspberry\TodoList\TodoList;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\TodoList\TodoList;
use Raspberry\TodoList\TodoListGateway;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Util\IdGenerator;

/**
 * @Covers Raspberry\TodoList\TodoList
 */
class TodoListTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var TodoList
	 */
	private $_subject;

	/**
	 * @var TodoListGateway|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockTodoListGateway;

	/**
	 * @var EventDispatcher|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockEventDispatcher;

	/**
	 * @var IdGenerator|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockIdGenerator;


	public function setUp() {
		parent::setUp();

		$this->_mockTodoListGateway = $this->getMock(TodoListGateway::class, [], [], '', false);
		$this->_mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);
		$this->_mockIdGenerator = $this->getMock(IdGenerator::class, [], [], '', false);

		$this->_subject = new TodoList($this->_mockTodoListGateway);
		$this->_subject->setEventDispatcher($this->_mockEventDispatcher);
		$this->_subject->setIdGenerator($this->_mockIdGenerator);
	}

	public function testAddItem() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->addItem($user, $item_vo);
	}

	public function testGetList() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->getList();
	}

	public function testGetItem() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->getItem($item_id);
	}

	public function testEditItem() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->editItem($item_id, $changes);
	}

	public function testDeleteItem() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$this->_subject->deleteItem($item_id);
	}

}
