<?php

namespace Tests\Raspberry\TodoList\ShoppingList;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\TodoList\ShoppingList;
use Raspberry\TodoList\ShoppingListGateway;

/**
 * @Covers Raspberry\TodoList\ShoppingList
 */
class ShoppingListTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var ShoppingList
	 */
	private $_subject;

	/**
	 * @var ShoppingListGateway|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockShoppingListGateway;

	public function setUp() {
		$this->_mockShoppingListGateway = $this->getMock(ShoppingListGateway::class, [], [], '', false);
		$this->_subject = new ShoppingList($this->_mockShoppingListGateway);
	}

	public function testGetShoppingListItems() {
		$list = [];

		$this->_mockShoppingListGateway
			->expects($this->once())
			->method('getShoppingListItems')
			->will($this->returnValue($list));

		$actual_result = $this->_subject->getShoppingListItems();
		$this->assertEquals($list, $actual_result);
	}

	public function testAddShoppingListItem() {
		$name = 'name';

		$this->_mockShoppingListGateway
			->expects($this->once())
			->method('addShoppingListItem')
			->with($name);

		$this->_subject->addShoppingListItem($name);
	}

	public function testRemoveShoppingListItem() {
		$name = 'name';

		$this->_mockShoppingListGateway
			->expects($this->once())
			->method('removeShoppingListItem')
			->with($name);

		$this->_subject->removeShoppingListItem($name);
	}

}
