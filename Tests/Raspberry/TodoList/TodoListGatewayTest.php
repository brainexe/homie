<?php

namespace Tests\Raspberry\TodoList\TodoListGateway;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\TodoList\TodoListGateway;
use Redis;

/**
 * @Covers Raspberry\TodoList\TodoListGateway
 */
class TodoListGatewayTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var TodoListGateway
	 */
	private $_subject;

	/**
	 * @var Redis|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockRedis;

	public function setUp() {

		$this->_mockRedis = $this->getMock(Redis::class, [], [], '', false);
		$this->_subject = new TodoListGateway();
		$this->_subject->setRedis($this->_mockRedis);
	}

	public function testAddItem() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$this->_subject->addItem($item_vo);
	}

	public function testGetList() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->getList();
	}

	public function testGetRawItem() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->getRawItem($item_id);
	}

	public function testEditItem() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$this->_subject->editItem($item_id, $changes);
	}

	public function testDeleteItem() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$this->_subject->deleteItem($item_id);
	}

}
