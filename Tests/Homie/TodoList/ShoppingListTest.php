<?php

namespace Tests\Homie\TodoList\ShoppingList;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\TodoList\ShoppingList;
use Homie\TodoList\ShoppingListGateway;

/**
 * @covers Homie\TodoList\ShoppingList
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
            ->willReturn($list);

        $actualResult = $this->subject->getShoppingListItems();
        $this->assertEquals($list, $actualResult);
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
