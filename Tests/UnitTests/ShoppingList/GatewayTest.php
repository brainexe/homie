<?php

namespace Tests\Homie\ShoppingList;

use BrainExe\Core\Redis\Predis;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Homie\ShoppingList\Gateway;

/**
 * @covers \Homie\ShoppingList\Gateway
 */
class GatewayTest extends TestCase
{

    use RedisMockTrait;

    /**
     * @var Gateway
     */
    private $subject;

    /**
     * @var Predis|MockObject
     */
    private $redis;

    public function setUp()
    {
        $this->redis = $this->getRedisMock();
        $this->subject = new Gateway();
        $this->subject->setRedis($this->redis);
    }

    public function testGetItems()
    {
        $items = [];

        $this->redis
            ->expects($this->once())
            ->method('smembers')
            ->with(Gateway::REDIS_KEY)
            ->willReturn($items);

        $actualResult = $this->subject->getItems();

        $this->assertEquals($items, $actualResult);
    }

    public function testAddItem()
    {
        $name = 'name';

        $this->redis
            ->expects($this->once())
            ->method('sadd')
            ->with(Gateway::REDIS_KEY, $name);

        $this->subject->addItem($name);
    }

    public function testRemoveItem()
    {
        $name = 'name';

        $this->redis
            ->expects($this->once())
            ->method('srem')
            ->with(Gateway::REDIS_KEY, $name);

        $this->subject->removeItem($name);
    }
}
