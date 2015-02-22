<?php

namespace Tests\Raspberry\Gpio\PinGateway;

use BrainExe\Core\Redis\RedisInterface;
use BrainExe\Tests\RedisMockTrait;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Raspberry\Gpio\PinGateway;

/**
 * @Covers Raspberry\Gpio\PinGateway
 */
class PinGatewayTest extends PHPUnit_Framework_TestCase
{

    use RedisMockTrait;

    /**
     * @var PinGateway
     */
    private $subject;

    /**
     * @var RedisInterface|MockObject
     */
    private $redis;

    public function setUp()
    {
        $this->redis = $this->getRedisMock();

        $this->subject = new PinGateway();
        $this->subject->setRedis($this->redis);
    }

    public function testGetPinDescriptions()
    {
        $descriptions = ['descriptions'];

        $this->redis
            ->expects($this->once())
            ->method('hGetAll')
            ->with(PinGateway::REDIS_PINS)
            ->willReturn($descriptions);

        $actualResult = $this->subject->getPinDescriptions();

        $this->assertEquals($descriptions, $actualResult);
    }
    public function testSetDescription()
    {
        $pinId       = 100;
        $description = 'test';

        $this->redis
            ->expects($this->once())
            ->method('hset')
            ->with(PinGateway::REDIS_PINS, $pinId, $description);

        $this->subject->setDescription($pinId, $description);
    }
}
