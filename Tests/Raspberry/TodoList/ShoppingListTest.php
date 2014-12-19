<?php

namespace Tests\Raspberry\TodoList\ShoppingList;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\TodoList\ShoppingList;
use Raspberry\TodoList\ShoppingListGateway;

/**
 * @Covers Raspberry\TodoList\ShoppingList
 */
class ShoppingListTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var ShoppingList
     */
    private $subject;

    /**
     * @var ShoppingListGateway|MockObject
     */
    private $mockShoppingListGateway;

    public function setUp()
    {
        $this->mockShoppingListGateway = $this->getMock(ShoppingListGateway::class, [], [], '', false);
        $this->subject = new ShoppingList($this->mockShoppingListGateway);
    }

    public function testGetShoppingListItems()
    {
        $list = [];

        $this->mockShoppingListGateway
        ->expects($this->once())
        ->method('getShoppingListItems')
        ->will($this->returnValue($list));

        $actual_result = $this->subject->getShoppingListItems();
        $this->assertEquals($list, $actual_result);
    }

    public function testAddShoppingListItem()
    {
        $name = 'name';

        $this->mockShoppingListGateway
        ->expects($this->once())
        ->method('addShoppingListItem')
        ->with($name);

        $this->subject->addShoppingListItem($name);
    }

    public function testRemoveShoppingListItem()
    {
        $name = 'name';

        $this->mockShoppingListGateway
        ->expects($this->once())
        ->method('removeShoppingListItem')
        ->with($name);

        $this->subject->removeShoppingListItem($name);
    }
}
