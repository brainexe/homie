<?php

namespace Tests\Raspberry\TodoList\TodoList;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\TodoList\TodoList;
use BrainExe\Core\Authentication\UserVO;
use Raspberry\TodoList\VO\TodoItemVO;
use Raspberry\TodoList\TodoListGateway;
use BrainExe\Core\EventDispatcher\EventDispatcher;
use BrainExe\Core\Util\IdGenerator;
use BrainExe\Core\Util\Time;

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

	/**
	 * @var Time|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockTime;

	public function setUp() {
		$this->_mockTodoListGateway = $this->getMock(TodoListGateway::class, [], [], '', false);
		$this->_mockEventDispatcher = $this->getMock(EventDispatcher::class, [], [], '', false);
		$this->_mockIdGenerator = $this->getMock(IdGenerator::class, [], [], '', false);
		$this->_mockTime = $this->getMock(Time::class, [], [], '', false);

		$this->_subject = new TodoList($this->_mockTodoListGateway);
		$this->_subject->setEventDispatcher($this->_mockEventDispatcher);
		$this->_subject->setIdGenerator($this->_mockIdGenerator);
		$this->_subject->setTime($this->_mockTime);
	}

	public function testAddItem() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$id = 11880;

		$this->_mockIdGenerator
			->expects($this->once())
			->method('generateRandomNumericId')
			->will($this->returnValue($id));

		$user = new UserVO();
		$item_vo = new TodoItemVO();

		$actual_result = $this->_subject->addItem($user, $item_vo);
	}

	public function testGetList() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->getList();
	}

	public function testGetItemWithEmptyResult() {
		$item_id = 10;

		$raw_item = [];

		$this->_mockTodoListGateway
			->expects($this->once())
			->method('getRawItem')
			->with($item_id)
			->will($this->returnValue($raw_item));

		$actual_result = $this->_subject->getItem($item_id);

		$this->assertNull($actual_result);
	}

	public function testGetItem() {
		$item_id = 10;

		$raw_item = [
			'id' => $id = 'id',
			'name' => $name = 'name',
			'user_id' => $user_id = 'user_id',
			'user_name' => $user_name = 'user_name',
			'description' => $description = 'description',
			'status' => $status = 'status',
			'deadline' => $deadline = 'deadline',
			'created_at' => $created_at = 'created_at',
			'last_change' => $last_change = 'last_change',
		];

		$this->_mockTodoListGateway
			->expects($this->once())
			->method('getRawItem')
			->with($item_id)
			->will($this->returnValue($raw_item));

		$actual_result = $this->_subject->getItem($item_id);

		$expected_item = new TodoItemVO();
		$expected_item->id = $id;
		$expected_item->name = $name;
		$expected_item->user_id = $user_id;
		$expected_item->user_name = $user_name;
		$expected_item->description = $description;
		$expected_item->status = $status;
		$expected_item->deadline = $deadline;
		$expected_item->created_at = $created_at;
		$expected_item->last_change = $last_change;

		$this->assertEquals($expected_item, $actual_result);
	}

	public function testEditItem() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$item_id = null;
		$changes = null;
		$actual_result = $this->_subject->editItem($item_id, $changes);
	}

	public function testDeleteItem() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$item_id = null;
		$this->_subject->deleteItem($item_id);
	}

}
