<?php

namespace Tests\Raspberry\TodoList\ShoppingListGateway;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Raspberry\TodoList\ShoppingListGateway;
use Redis;

/**
 * @Covers Raspberry\TodoList\ShoppingListGateway
 */
class ShoppingListGatewayTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var ShoppingListGateway
	 */
	private $_subject;

	/**
	 * @var Redis|PHPUnit_Framework_MockObject_MockObject
	 */
	private $_mockRedis;

	public function setUp() {
		$this->_mockRedis = $this->getMock(Redis::class, [], [], '', false);
		$this->_subject = new ShoppingListGateway();
		$this->_subject->setRedis($this->_mockRedis);
	}

	public function testGetShoppingListItems() {
		$items = [];

		$this->_mockRedis
			->expects($this->once())
			->method('sMembers')
			->with(ShoppingListGateway::REDIS_KEY)
			->will($this->returnValue($items));

		$actual_result = $this->_subject->getShoppingListItems();

		$this->assertEquals($items, $actual_result);
	}

	public function testAddShoppingListItem() {
		$name = 'name';

		$this->_mockRedis
			->expects($this->once())
			->method('sAdd')
			->with(ShoppingListGateway::REDIS_KEY, $name);

		$this->_subject->addShoppingListItem($name);
	}

	public function testRemoveShoppingListItem() {
		$name = 'name';

		$this->_mockRedis
			->expects($this->once())
			->method('sRem')
			->with(ShoppingListGateway::REDIS_KEY, $name);

		$this->_subject->removeShoppingListItem($name);
	}

}
