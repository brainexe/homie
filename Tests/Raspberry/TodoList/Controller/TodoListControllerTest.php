<?php

namespace Tests\Raspberry\TodoList\Controller\TodoListController;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\TodoList\Controller\TodoListController;
use Raspberry\TodoList\TodoList;
use BrainExe\Core\Authentication\DatabaseUserProvider;
use Raspberry\TodoList\ShoppingList;

/**
 * @Covers Raspberry\TodoList\Controller\TodoListController
 */
class TodoListControllerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var TodoListController
	 */
	private $_subject;

	/**
	 * @var TodoList|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockTodoList;

	/**
	 * @var DatabaseUserProvider|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockDatabaseUserProvider;

	/**
	 * @var ShoppingList|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockShoppingList;


	public function setUp() {
		$this->_mockTodoList = $this->getMock(TodoList::class, [], [], '', false);
		$this->_mockDatabaseUserProvider = $this->getMock(DatabaseUserProvider::class, [], [], '', false);
		$this->_mockShoppingList = $this->getMock(ShoppingList::class, [], [], '', false);

		$this->_subject = new TodoListController($this->_mockTodoList, $this->_mockDatabaseUserProvider, $this->_mockShoppingList);
	}

	public function testIndex() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->index($request);
	}

	public function testFetchList() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$list = [];

		$this->_mockTodoList
			->expects($this->once())
			->method('getList')
			->will($this->returnValue($list));

		$actual_result = $this->_subject->fetchList();
		$this->assertEquals($list, $actual_result);
	}

	public function testAddItem() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->addItem($request);
	}

	public function testAddShoppingListItem() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->addShoppingListItem($request);
	}

	public function testRemoveShoppingListItem() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->removeShoppingListItem($request);
	}

	public function testSetItemStatus() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->setItemStatus($request);
	}

	public function testSetAssignee() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->setAssignee($request);
	}

	public function testDeleteItem() {
		$this->markTestIncomplete('This is only a dummy implementation');

		$actual_result = $this->_subject->deleteItem($request);
	}

}
