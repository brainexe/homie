<?php

namespace Tests\Homie\TodoList;

use BrainExe\Core\Redis\Predis;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\TodoList\ShoppingListGateway;

/**
 * @covers Homie\TodoList\ShoppingListGateway
 */
class ShoppingListGatewayTest extends PHPUnit_Framework_TestCase
{

    use RedisMockTrait;

    /**
     * @var ShoppingListGateway
     */
    private $subject;

    /**
     * @var Predis|MockObject
     */
    private $redis;

    public function setUp()
    {
        $this->redis = $this->getRedisMock();
        $this->subject = new ShoppingListGateway();
        $this->subject->setRedis($this->redis);
    }

    public function testGetShoppingListItems()
    {
        $items = [];

        $this->redis
            ->expects($this->once())
            ->method('sMembers')
            ->with(ShoppingListGateway::REDIS_KEY)
            ->willReturn($items);

        $actualResult = $this->subject->getShoppingListItems();

        $this->assertEquals($items, $actualResult);
    }

    public function testAddShoppingListItem()
    {
        $name = 'name';

        $this->redis
            ->expects($this->once())
            ->method('sAdd')
            ->with(ShoppingListGateway::REDIS_KEY, $name);

        $this->subject->addShoppingListItem($name);
    }

    public function testRemoveShoppingListItem()
    {
        $name = 'name';

        $this->redis
            ->expects($this->once())
            ->method('sRem')
            ->with(ShoppingListGateway::REDIS_KEY, $name);

        $this->subject->removeShoppingListItem($name);
    }
}
