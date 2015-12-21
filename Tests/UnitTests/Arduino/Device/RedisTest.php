<?php

namespace Tests\Homie\Arduino;

use BrainExe\Tests\RedisMockTrait;
use Homie\Arduino\Device\Redis;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Homie\Arduino\SerialEvent;

use Predis\Client;

/**
 * @covers Homie\Arduino\Device\Redis
 */
class RedisTest extends TestCase
{

    use RedisMockTrait;

    /**
     * @var Redis
     */
    private $subject;

    /**
     * @var Client|MockObject
     */
    private $redis;

    public function setUp()
    {
        $this->redis    = $this->getRedisMock();
        $this->subject = new Redis();
        $this->subject->setRedis($this->redis);
    }

    /**
     * @dataProvider provideActions
     * @param string $action
     * @param int $pin
     * @param int $value
     * @param string $expected
     */
    public function testSendSerial($action, $pin, $value, $expected)
    {
        $this->redis
            ->expects($this->once())
            ->method('publish')
            ->with(Redis::REDIS_CHANNEL, $expected);

        $event = new SerialEvent($action, $pin, $value);

        $this->subject->sendSerial($event);
    }

    /**
     * @return array[]
     */
    public function provideActions()
    {
        return [
            ['a', 12, 1, "a:12:1"],
            ['a', 100000, -121, "a:100000:-121"],
            ['s', 0, 0, "s:0:0"],
            ['s', null, false, "s:0:0"],
        ];
    }
}
