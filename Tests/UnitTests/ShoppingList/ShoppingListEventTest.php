<?php

namespace Tests\Homie\ShoppingList;

use Homie\ShoppingList\ShoppingListEvent;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Homie\ShoppingList\ShoppingListEvent
 */
class ShoppingListEventTest extends TestCase
{

    public function testConstruct()
    {
        $event = new ShoppingListEvent(ShoppingListEvent::ADD, 'test');

        $this->assertEquals(ShoppingListEvent::ADD, $event->getEventName());
        $this->assertEquals('test', $event->getItem());
    }
}
