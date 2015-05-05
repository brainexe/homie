<?php

namespace Tests\Homie\TodoList;

use BrainExe\Core\Redis\RedisInterface;
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
     * @var RedisInterface|MockObject
     */
    private $mockRedis;

    public function setUp()
    {
        $this->mockRedis = $this->getRedisMock();
        $this->subject = new ShoppingListGateway();
        $this->subject->setRedis($this->mockRedis);
    }

    public function testGetShoppingListItems()
    {
        $items = [];

        $this->mockRedis
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

        $this->mockRedis
            ->expects($this->once())
            ->method('sAdd')
            ->with(ShoppingListGateway::REDIS_KEY, $name);

        $this->subject->addShoppingListItem($name);
    }

    public function testRemoveShoppingListItem()
    {
        $name = 'name';

        $this->mockRedis
            ->expects($this->once())
            ->method('sRem')
            ->with(ShoppingListGateway::REDIS_KEY, $name);

        $this->subject->removeShoppingListItem($name);
    }
}
