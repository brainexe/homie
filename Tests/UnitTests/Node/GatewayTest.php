<?php

namespace Tests\Homie\Node;

use BrainExe\Core\Redis\Predis;
use BrainExe\Tests\RedisMockTrait;
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
        $result = ['array'];

        $this->redis
            ->expects($this->once())
            ->method('hgetall')
            ->with(Gateway::REDIS_KEY)
            ->willReturn($result);

        $actual = $this->subject->getAll();

        $this->assertEquals($result, $actual);
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

    public function testSet()
    {
        $nodeId = 12;
        $data = ['foo' => 'bar'];

        $this->redis
            ->expects($this->once())
            ->method('hset')
            ->with(Gateway::REDIS_KEY, $nodeId, '{"foo":"bar"}');

        $this->subject->set($nodeId, $data);
    }
}
