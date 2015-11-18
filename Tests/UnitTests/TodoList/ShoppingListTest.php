<?php

namespace Tests\Homie\TodoList;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\TodoList\ShoppingList;
use Homie\TodoList\Gateway;

/**
 * @covers Homie\TodoList\ShoppingList
 */
class ShoppingListTest extends TestCase
{

    /**
     * @var ShoppingList
     */
    private $subject;

    /**
     * @var Gateway|MockObject
     */
    private $gateway;

    public function setUp()
    {
        $this->gateway = $this->getMock(Gateway::class, [], [], '', false);
        $this->subject = new ShoppingList($this->gateway);
    }

    public function testGetItems()
    {
        $list = [];

        $this->gateway
            ->expects($this->once())
            ->method('getItems')
            ->willReturn($list);

        $actualResult = $this->subject->getItems();
        $this->assertEquals($list, $actualResult);
    }

    public function testAddItem()
    {
        $name = 'name';

        $this->gateway
            ->expects($this->once())
            ->method('addItem')
            ->with($name);

        $this->subject->addItem($name);
    }

    public function testRemoveItem()
    {
        $name = 'name';

        $this->gateway
            ->expects($this->once())
            ->method('removeItem')
            ->with($name);

        $this->subject->removeItem($name);
    }
}
