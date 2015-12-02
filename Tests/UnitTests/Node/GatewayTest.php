<?php

namespace Tests\Homie\Node;

use BrainExe\Core\Redis\Predis;
use BrainExe\Tests\RedisMockTrait;
use Homie\Node;
use Homie\Node\Gateway;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * @covers Homie\Node\Gateway
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

    public function testGetAll()
    {
        $result = [42 => 'O:10:"Homie\\Node":0:{}'];
        $this->redis
            ->expects($this->once())
            ->method('hgetall')
            ->with(Gateway::REDIS_KEY)
            ->willReturn($result);

        $actual = $this->subject->getAll();

        $this->assertCount(1, $actual);
        $this->assertInstanceOf(Node::class, $actual[42]);
    }

    public function testGet()
    {
        $result = 'O:10:"Homie\\Node":0:{}';
        $this->redis
            ->expects($this->once())
            ->method('hget')
            ->with(Gateway::REDIS_KEY, 42)
            ->willReturn($result);

        $actual = $this->subject->get(42);

        $this->assertInstanceOf(Node::class, $actual);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Invalid node: 42
     */
    public function testGetInvalid()
    {
        $this->redis
            ->expects($this->once())
            ->method('hget')
            ->with(Gateway::REDIS_KEY, 42)
            ->willReturn('');

        $this->subject->get(42);
    }

    public function testDelete()
    {
        $nodeId = 12;

        $this->redis
            ->expects($this->once())
            ->method('hdel')
            ->with(Gateway::REDIS_KEY, [$nodeId]);

        $this->subject->delete($nodeId);
    }

    public function testSave()
    {
        $nodeId = 12;

        $this->redis
            ->expects($this->once())
            ->method('hset')
            ->with(Gateway::REDIS_KEY, $nodeId);

        $node = new Node($nodeId, 'type', 'name', 'address');
        $this->subject->save($node);
    }
}
