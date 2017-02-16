<?php

namespace Tests\Homie\ShoppingList;

use BrainExe\Core\EventDispatcher\EventDispatcher;
use Homie\ShoppingList\ShoppingListEvent;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\ShoppingList\ShoppingList;
use Homie\ShoppingList\Gateway;

/**
 * @covers \Homie\ShoppingList\ShoppingList
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

    /**
     * @var EventDispatcher|MockObject
     */
    private $dispatcher;

    public function setUp()
    {
        $this->gateway    = $this->createMock(Gateway::class);
        $this->dispatcher = $this->createMock(EventDispatcher::class);

        $this->subject = new ShoppingList($this->gateway);
        $this->subject->setEventDispatcher($this->dispatcher);
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

        $event = new ShoppingListEvent(ShoppingListEvent::ADD, $name);
        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($event);

        $this->subject->addItem($name);
    }

    public function testRemoveItem()
    {
        $name = 'name';

        $this->gateway
            ->expects($this->once())
            ->method('removeItem')
            ->with($name);

        $event = new ShoppingListEvent(ShoppingListEvent::REMOVE, $name);
        $this->dispatcher
            ->expects($this->once())
            ->method('dispatchEvent')
            ->with($event);
        $this->subject->removeItem($name);
    }
}
